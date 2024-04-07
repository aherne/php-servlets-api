<?php

namespace Lucinda\STDOUT;

use Lucinda\STDOUT\Request\UploadedFiles\Exception as FileUploadException;
use Lucinda\MVC\Runnable;
use Lucinda\MVC\Response;
use Lucinda\MVC\ConfigurationException;
use Lucinda\MVC\Application\Format;

/**
 * Implements STDOUT front controller MVC functionality, integrating all API components as a whole.
 */
class FrontController implements Runnable
{
    protected string $documentDescriptor;
    protected Attributes $attributes;
    /**
     * @var array<string, string[]>
     */
    protected array $events = [];

    /**
     * Starts API front controller, setting up necessary variables
     *
     * @param Attributes $attributes
     * @param string     $documentDescriptor
     */
    public function __construct(string $documentDescriptor, Attributes $attributes)
    {
        // saves arguments
        $this->documentDescriptor = $documentDescriptor;
        $this->attributes = $attributes;

        // initialize events
        $this->events = [
            EventType::START->value=>[],
            EventType::APPLICATION->value=>[],
            EventType::REQUEST->value=>["\\Lucinda\\STDOUT\\EventListeners\\RequestValidator"],
            EventType::RESPONSE->value=>[],
            EventType::END->value=>[]
        ];
    }

    /**
     * Adds an event listener
     *
     * @param EventType $type      One of EventType enum values
     * @param string    $className Name of event listener class (including namespace and subfolder, if any)
     */
    public function addEventListener(EventType $type, string $className): void
    {
        $this->events[$type->value][] = $className;
    }

    /**
     * Performs all steps required to convert request to response in procedural mode, while delegating to
     * subcomponents, to maximize performance
     *
     * @throws ConfigurationException If any other situation where execution cannot continue.
     */
    public function run(): void
    {
        // execute events for START
        $this->runStartEvents();

        // reads XML configuration file
        $application = new Application($this->documentDescriptor);

        // execute events for APPLICATION
        $this->runApplicationEvents($application);

        // reads user request, into request (RO), session (RW) and cookies (RW) objects
        $request = new Request();
        $session = new Session($application->getSessionOptions());
        $cookies = new Cookies($application->getCookieOptions());

        // execute events for REQUEST
        $this->runRequestEvents($application, $request, $session, $cookies);

        // determine response format
        $format = $application->resolvers($this->attributes->getValidFormat());

        // initializes response
        $response = $this->generateResponse($application, $format);

        // locates and runs page controller
        $this->runController($application, $request, $session, $cookies, $response);

        // resolves view into response body, unless output stream has been written to already
        $this->runViewResolver($application, $format, $response);

        // execute events for RESPONSE
        $this->runResponseEvents($application, $request, $session, $cookies, $response);

        // commits response to caller
        $response->commit();

        // execute events for END
        $this->runEndEvents($application, $request, $session, $cookies, $response);
    }

    /**
     * Generates response object based on content type and template file
     *
     * @param Application $application
     * @param Format $format
     * @return Response
     */
    protected function generateResponse(Application $application, Format $format): Response
    {
        $charset = $format->getCharacterEncoding();
        $contentType = $format->getContentType().($charset?"; charset=".$charset:"");

        $template = $application->routes($this->attributes->getValidPage())->getView();
        $templateFile = $template?$application->getViewsPath()."/".$template:"";

        return new Response($contentType, $templateFile);
    }

    /**
     * Executes all event listeners set to be run before any handling of request
     *
     * @return void
     */
    protected function runStartEvents(): void
    {
        foreach ($this->events[EventType::START->value] as $className) {
            $runnable = new $className($this->attributes);
            $runnable->run();
        }
    }

    /**
     * Executes all event listeners set to be run after application configuration is read
     *
     * @param Application $application
     * @return void
     */
    protected function runApplicationEvents(
        Application $application
    ): void
    {
        foreach ($this->events[EventType::APPLICATION->value] as $className) {
            $runnable = new $className($this->attributes, $application);
            $runnable->run();
        }
    }

    /**
     * Executes all event listeners set to be run after request information is read
     *
     * @param Application $application
     * @param Request $request
     * @param Session $session
     * @param Cookies $cookies
     * @return void
     */
    protected function runRequestEvents(
        Application $application,
        Request $request,
        Session $session,
        Cookies $cookies
    ): void
    {
        foreach ($this->events[EventType::REQUEST->value] as $className) {
            $runnable = new $className($this->attributes, $application, $request, $session, $cookies);
            $runnable->run();
        }
    }

    /**
     * Executes all event listeners set to be run after response is compiled
     *
     * @param Application $application
     * @param Request $request
     * @param Session $session
     * @param Cookies $cookies
     * @param Response $response
     * @return void
     */
    protected function runResponseEvents(
        Application $application,
        Request $request,
        Session $session,
        Cookies $cookies,
        Response $response
    ): void
    {
        foreach ($this->events[EventType::RESPONSE->value] as $className) {
            $runnable = new $className($this->attributes, $application, $request, $session, $cookies, $response);
            $runnable->run();
        }
    }

    /**
     * Executes all event listeners set to be run after response is committed to caller
     *
     * @param Application $application
     * @param Request $request
     * @param Session $session
     * @param Cookies $cookies
     * @param Response $response
     * @return void
     */
    protected function runEndEvents(
        Application $application,
        Request $request,
        Session $session,
        Cookies $cookies,
        Response $response
    ): void
    {
        foreach ($this->events[EventType::END->value] as $className) {
            $runnable = new $className($this->attributes, $application, $request, $session, $cookies, $response);
            $runnable->run();
        }
    }

    /**
     * Detects and executes page controller, if any
     *
     * @param Application $application
     * @param Request $request
     * @param Session $session
     * @param Cookies $cookies
     * @param Response $response
     * @return void
     */
    protected function runController(
        Application $application,
        Request $request,
        Session $session,
        Cookies $cookies,
        Response $response
    ): void
    {
        if ($className  = $application->routes($this->attributes->getValidPage())->getController()) {
            $runnable = new $className($this->attributes, $application, $request, $session, $cookies, $response);
            $runnable->run();
        }
    }

    /**
     * Detects resolver to compile view into response body, if not already written
     *
     * @param Application $application
     * @param Format $format
     * @param Response $response
     * @return void
     */
    protected function runViewResolver(
        Application $application,
        Format $format,
        Response $response
    ): void
    {
        if ($response->getBody()===null) {
            $className  = $format->getViewResolver();
            $runnable = new $className($application, $response);
            $runnable->run();
        }
    }
}
