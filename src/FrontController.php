<?php
namespace Lucinda\MVC\STDOUT;

require_once("Runnable.php");
require_once("Controller.php");
require_once("Application.php");
require_once("Request.php");
require_once("Response.php");
require_once("request/PageValidator.php");
require_once("response/ViewResolver.php");
require_once("locators/ListenerLocator.php");
require_once("locators/ControllerLocator.php");
require_once("locators/ViewResolverLocator.php");
require_once("listeners/ApplicationListener.php");
require_once("listeners/RequestListener.php");
require_once("listeners/ResponseListener.php");

/**
 * Implements STDOUT front controller MVC functionality, integrating all API components as a whole.
 */
class FrontController {
    /**
     * Performs all steps required to convert request to response in procedural mode, while delegating to subcomponents, to maximize performance
     *
     * @param string $documentDescriptor Path to XML file that configures API
     * @throws XMLException If xml contents fail validation checks.
     * @throws FormatNotFoundException If an invalid response format was setup by developer in XML for route requested by client.
     * @throws PathNotFoundException If an invalid route was requested from client or setup by developer in XML.
     * @throws FileUploadException If file upload failed due to server constraints.
     * @throws ServletException If any other situation where execution cannot continue.
     */
    public function __construct($documentDescriptor="configuration.xml") {
        // sets application object based on user-defined XML
        $application = new Application($documentDescriptor);

        // instances library that finds event listeners for each lifecycle event
        $listenerLocator = new ListenerLocator($application);

        // runs ApplicationListener instances found by locator in the order they were set in xml
        $listeners = $listenerLocator->getClassNames("ApplicationListener");
        foreach($listeners as $className) {
            $runnable = new $className($application);
            $runnable->run();
        }

        // sets request object based on user request information matched with that in XML
        $request = new Request();
        $request->setValidator(new PageValidator($request->getURI()->getPage(), $application));

        // runs RequestListener instances found by locator in the order they were set in xml
        $listeners = $listenerLocator->getClassNames("RequestListener");
        foreach($listeners as $className) {
            $runnable = new $className($application, $request);
            $runnable->run();
        }

        // sets response object by matching validated user request information to that contained in XML
        $format = $application->formats($request->getValidator()->getFormat());
        $contentType = $format->getContentType().($format->getCharacterEncoding()?"; charset=".$format->getCharacterEncoding():"");
        $response = new Response($contentType);
        if(!$application->getAutoRouting()) {
            $view = $application->routes($request->getValidator()->getPage())->getView();
            if($view) {
                $response->setView($view);
            }
        }

        // locates and runs page controller
        $controllerLocator = new ControllerLocator($application, $request->getValidator()->getPage());
        $className  = $controllerLocator->getClassName();
        if($className) {
            $runnable = new $className($application, $request, $response);
            $runnable->run();
        }

        // if response is not disabled, produce a view
        if(!$response->isDisabled()) {
            // locates a view resolver for response content type that populates output stream when ran
            if($response->getOutputStream()->isEmpty()) {
                $viewResolverLocator = new ViewResolverLocator($application, $request->getValidator()->getFormat());
                $className  = $viewResolverLocator->getClassName();
                if($className) {
                    $runnable = new $className($application, $response);
                    $runnable->run();
                }
            }

            // runs ResponseListener instances found by locator in the order they were set in xml
            $listeners = $listenerLocator->getClassNames("ResponseListener");
            foreach($listeners as $className) {
                $runnable = new $className($application, $request, $response);
                $runnable->run();
            }
        }

        // commits response to requester
        $response->commit();
    }
}
