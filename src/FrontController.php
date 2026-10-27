<?php

namespace Lucinda\STDOUT;

use Lucinda\MVC\ConfigurationException;
use Lucinda\MVC\Runnable;
use Lucinda\MVC\Controller\ViewAware;
use Lucinda\MVC\EventListener\Faceted;
use Lucinda\MVC\EventListener\MultiFaceted;
use Lucinda\MVC\EventListener\UnFaceted;
use Lucinda\MVC\EventScheduler;
use Lucinda\MVC\EventType;
use Lucinda\MVC\FacetRegistry;
use Lucinda\MVC\ReflectionInjector;
use Lucinda\MVC\Response\Transformer\Body as TransformerBody;
use Lucinda\MVC\Response\Transformer\Status as TransformerStatus;
use Lucinda\MVC\Response\Transformer\Headers as TransformerHeaders;
use Lucinda\MVC\Response\View;
use Lucinda\MVC\TerminationException;
use Lucinda\MVC\Service\ResolverInfoDetector;
use Lucinda\MVC\Service\ViewDetector;
use Lucinda\MVC\XmlTags\ResolverInfo;
use Lucinda\MVC\Response\Http as HttpResponse;
use Lucinda\STDOUT\Validators\ValidatedRequest;
use Lucinda\STDOUT\Service\ContentTypeDetector;

/**
 * Coordinates the complete HTTP request-to-response MVC lifecycle
 *
 * A run loads XML configuration, creates injectable request, session, cookie,
 * and validated-request facets, executes scheduled lifecycle listeners, invokes
 * the selected controller, resolves its view, applies response transformers,
 * and emits the resulting HTTP response.
 *
 * RESTful controllers are dispatched by HTTP method through RestfulWrapper;
 * conventional ViewAware controllers continue to run through their run()
 * method. A TerminationException short-circuits the normal lifecycle and emits
 * its embedded response immediately.
 *
 * @see RestfulWrapper
 * @see EventScheduler
 */
final class FrontController implements Runnable
{
    /**
     * Path to the root XML application document
     */
    protected string $documentDescriptor;

    /**
     * Facets available for listener and controller constructor injection
     */
    protected FacetRegistry $facetRegistry;

    /**
     * Constructor injector backed by the current facet registry
     */
    protected ReflectionInjector $reflectionInjector;

    /**
     * Listener classes grouped by MVC lifecycle event
     */
    protected EventScheduler $eventScheduler;

    /**
     * Creates a front controller for one application configuration
     *
     * @param string $documentDescriptor Path to the root XML application document
     * @param EventScheduler $eventScheduler Lifecycle listeners to execute for each request
     */
    public function __construct(
        string $documentDescriptor,
        EventScheduler $eventScheduler
    )
    {
        // saves arguments
        $this->documentDescriptor = $documentDescriptor;
        $this->facetRegistry = new FacetRegistry();
        $this->reflectionInjector = new ReflectionInjector($this->facetRegistry);
        $this->eventScheduler = $eventScheduler;
    }

    /**
     * Converts the current PHP request globals into an emitted HTTP response
     *
     * @throws \Throwable If configuration, validation, injection, controller execution, or response processing fails
     */
    public function run(): void
    {
        try {
            // execute events for START
            $this->runEvents(EventType::START);

            // reads XML configuration file
            $application = new Application($this->documentDescriptor);
            $this->facetRegistry->put($application->getApplicationInfo());

            // execute events for APPLICATION
            $this->runEvents(EventType::APPLICATION);

            // reads user request, into request (RO), session (RW) and cookies (RW) objects
            $request = new Request();
            $this->facetRegistry->put($request);
            $session = new Session($application->getSessionOptions());
            $this->facetRegistry->put($session);
            $cookies = new Cookies($application->getCookieOptions());
            $this->facetRegistry->put($cookies);

            // validate request
            $requestValidator = new ValidatedRequest($application, $request);
            $this->facetRegistry->put($requestValidator);

            // execute events for REQUEST
            $this->runEvents(EventType::REQUEST);

            // determine response format
            $responseInfoDetector = new ResolverInfoDetector($application, $requestValidator);
            $contentTypeDetector = new ContentTypeDetector($responseInfoDetector->getResolver());
            $response = new HttpResponse();
            $response->setHeader("Content-Type", $contentTypeDetector->getContentType());

            // locates and runs page controller and sets up view
            $filledView = $this->runController($application, $requestValidator, $request);
            $viewDetector = new ViewDetector($application, $requestValidator, $filledView);
            $view = $viewDetector->getView();

            // resolves view into response body, unless output stream has been written to already
            if ($view!==null) {
                $this->runViewResolver($responseInfoDetector->getResolver(), $response, $view);
            }

            // execute events for RESPONSE
            $this->runResponseTransformers($response);

            // commits response to caller
            $response->run();

            // execute events for END            
            $this->runEvents(EventType::END);
        } catch (TerminationException $e) {
            $e->getResponse()->run();
        }
    }

    /**
     * Executes listeners registered for one lifecycle event
     *
     * Faceted listeners publish their returned facet by type. Multi-faceted
     * listeners publish each returned value under its explicit identifier.
     *
     * @param EventType $eventType Lifecycle phase to execute
     * @throws \Throwable If listener construction or execution fails
     */
    protected function runEvents(EventType $eventType): void
    {
        $eventsToRun = $this->eventScheduler->get($eventType);      
        foreach ($eventsToRun as $className) {
            $object = $this->reflectionInjector->create($className);
            if ($object instanceof Faceted) {
                $this->facetRegistry->put($object->run());
            } else if ($object instanceof MultiFaceted) {
                $facets = $object->run()->getAll();
                foreach ($facets as $id=>$facet) {
                    $this->facetRegistry->putAs($id, $facet);
                }
            } else if ($object instanceof UnFaceted) {
                $object->run();
            } else {
                throw new ConfigurationException("Unsupported listener contract: ".$className);
            }
        }
    }

    /**
     * Applies body, status, and header transformations from RESPONSE listeners
     *
     * A listener may implement more than one transformer contract; every
     * matching transformation is applied in scheduler order.
     *
     * @param HttpResponse $response Mutable response being prepared for emission
     * @throws \Throwable If listener construction or response transformation fails
     */
    protected function runResponseTransformers(HttpResponse $response): void
    {
        $eventsToRun = $this->eventScheduler->get(EventType::RESPONSE);
        foreach ($eventsToRun as $className) {
            $object = $this->reflectionInjector->create($className);
            if ($object instanceof TransformerBody) {
                $response->transformBody($object);
            }
            if ($object instanceof TransformerStatus) {
                $response->setStatus($object->getHttpStatus());
            }
            if ($object instanceof TransformerHeaders) {
                $list = $object->getExtraHeaders()->get();
                foreach ($list as $key=>$value) {
                    $response->setHeader($key, $value);
                }
            }
        }
    }

    /**
     * Instantiates and executes the controller configured for the validated route
     *
     * RESTful controllers take precedence over conventional ViewAware handling.
     * Controllers without a view contract are executed for side effects and
     * produce no explicit view.
     *
     * @param Application $application Parsed application configuration
     * @param ValidatedRequest $validatedRequest Route and response format selected for the request
     * @param Request $request Current immutable HTTP request
     * @return View|null Controller-provided view, or null when none is produced
     * @throws \Throwable If controller injection, dispatch, or execution fails
     */
    protected function runController(
        Application $application,
        ValidatedRequest $validatedRequest,
        Request $request
    ): ?View
    {
        if ($className  = $application->getRoutes($validatedRequest->getRoute())->getController()) {
            $object = $this->reflectionInjector->create($className);
            if ($object instanceof Restful) {
                $restWrapper = new RestfulWrapper($object, $request);
                return $restWrapper->getResult();
            } elseif ($object instanceof ViewAware) {
                return $object->run();
            } else {
                $object->run();
            }
        }
        return null;
    }

    /**
     * Resolves a controller or route view into the response body
     *
     * @param ResolverInfo $resolverInfo Resolver configuration selected for the response format
     * @param HttpResponse $response Mutable response receiving the resolved body
     * @param View $view View data and optional template selected for rendering
     * @throws \Throwable If resolver injection or rendering fails
     */
    protected function runViewResolver(
        ResolverInfo $resolverInfo,
        HttpResponse $response,
        View $view
    ): void {
        $resolver = $this->reflectionInjector->create($resolverInfo->getViewResolver());
        $response->resolve($view, $resolver);
    }
}
