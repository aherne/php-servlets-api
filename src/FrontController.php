<?php
namespace Lucinda\STDOUT;

use Lucinda\STDOUT\Request\UploadedFiles\Exception as FileUploadException;
use Lucinda\STDOUT\Locators\ControllerLocator;
use Lucinda\STDOUT\Locators\ViewResolverLocator;
use Lucinda\STDOUT\Locators\EventListenerLocator;

/**
 * Implements STDOUT front controller MVC functionality, integrating all API components as a whole.
 */
class FrontController implements Runnable
{
    private $documentDescriptor;
    private $attributes;
    private $events = [];
    
    /**
     * Starts API front controller, setting up necessary variables
     *
     * @param Attributes $attributes
     * @param string $documentDescriptor
     */
    public function __construct(Attributes $attributes, string $documentDescriptor = "stdout.xml"): void
    {
        $this->documentDescriptor = $documentDescriptor;
        $this->attributes = $attributes;
        // initialize events
        $this->events = [
            EventType::START=>[],
            EventType::APPLICATION=>[],
            EventType::REQUEST=>[[__DIR__."/EventListeners/RequestValidator"=>"\\Lucinda\\STDOUT\\EventListeners"]],
            EventType::SESSION=>[],
            EventType::COOKIES=>[],
            EventType::RESPONSE=>[],
            EventType::END=>[]
        ];
    }
    
    /**
     * Adds an event listener
     *
     * @param string $type One of EventType enum values
     * @param string $classPath Absolute location of class file or relative to project root without php extension
     * @param string $namespace Namespace class belongs to, unless it belongs to global namespace
     */
    public function addEventListener(string $type, string $classPath, string $namespace=""): void
    {
        $this->events[$type][] = [$classPath=>$namespace];
    }
    
    /**
     * Performs all steps required to convert request to response in procedural mode, while delegating to subcomponents, to maximize performance
     *
     * @throws FormatNotFoundException If an invalid response format was setup by developer in XML for route requested by client.
     * @throws PathNotFoundException If an invalid route was requested from client or setup by developer in XML.
     * @throws FileUploadException If file upload failed due to server constraints.
     * @throws Exception If any other situation where execution cannot continue.
     */
    public function run(): void
    {
        // execute events for START
        foreach ($this->events[EventType::START] as $path=>$namespace) {
            $eventLocator = new EventListenerLocator($path, $namespace, "\\Lucinda\\STDOUT\\EventListeners\\Start");
            $className = $eventLocator->getClassName();
            $runnable = new $className($this->attributes);
            $runnable->run();
        }
        
        // reads XML configuration file
        $application = new Application($this->documentDescriptor);
        
        // execute events for APPLICATION
        foreach ($this->events[EventType::APPLICATION] as $path=>$namespace) {
            $eventLocator = new EventListenerLocator($path, $namespace, "\\Lucinda\\STDOUT\\EventListeners\\Application");
            $className = $eventLocator->getClassName();
            $runnable = new $className($this->attributes, $application);
            $runnable->run();
        }
        
        // reads user request
        $request = new Request();
        
        // execute events for REQUEST
        foreach ($this->events[EventType::REQUEST] as $path=>$namespace) {
            $eventLocator = new EventListenerLocator($path, $namespace, "\\Lucinda\\STDOUT\\EventListeners\\Request");
            $className = $eventLocator->getClassName();
            $runnable = new $className($this->attributes, $application, $request);
            $runnable->run();
        }
        
        // encapsulates session operations
        $session = new Session();
        
        // execute events for SESSION
        foreach ($this->events[EventType::SESSION] as $path=>$namespace) {
            $eventLocator = new EventListenerLocator($path, $namespace, "\\Lucinda\\STDOUT\\EventListeners\\Session");
            $className = $eventLocator->getClassName();
            $runnable = new $className($this->attributes, $application, $request, $session);
            $runnable->run();
        }
        
        // encapsulates cookies operations
        $cookies = new Cookies();
        
        // execute events for COOKIES
        foreach ($this->events[EventType::COOKIES] as $path=>$namespace) {
            $eventLocator = new EventListenerLocator($path, $namespace, "\\Lucinda\\STDOUT\\EventListeners\\Cookies");
            $className = $eventLocator->getClassName();
            $runnable = new $className($this->attributes, $application, $request, $session, $cookies);
            $runnable->run();
        }
                
        // sets view
        $view = new View($this->getTemplateFile($application));

        // locates and runs page controller
        $controllerLocator = new ControllerLocator($application, $this->attributes);
        $className  = $controllerLocator->getClassName();
        if ($className) {
            $runnable = new $className($this->attributes, $application, $request, $session, $cookies, $view);
            $runnable->run();
        }
        
        // sets response object by matching validated user request information to that contained in XML
        $response = new Response($this->getContentType($application));

        // set up response based on view
        $viewResolverLocator = new ViewResolverLocator($application, $this->attributes);
        $className  = $viewResolverLocator->getClassName();
        if ($className) {
            $runnable = new $className($application, $view);
            $runnable->run();
        }
        
        // execute events for RESPONSE
        foreach ($this->events[EventType::RESPONSE] as $path=>$namespace) {
            $eventLocator = new EventListenerLocator($path, $namespace, "\\Lucinda\\STDOUT\\EventListeners\\Response");
            $className = $eventLocator->getClassName();
            $runnable = new $className($this->attributes, $application, $request, $session, $cookies, $response);
            $runnable->run();
        }

        // commits response to caller
        $response->commit();
        
        // execute events for END
        foreach ($this->events[EventType::END] as $path=>$namespace) {
            $eventLocator = new EventListenerLocator($path, $namespace, "\\Lucinda\\STDOUT\\EventListeners\\End");
            $className = $eventLocator->getClassName();
            $runnable = new $className($this->attributes, $application, $request, $session, $cookies, $response);
            $runnable->run();
        }
    }
    
    /**
     * Gets response template file
     *
     * @param Application $application
     * @return string
     */
    private function getTemplateFile(Application $application): string
    {
        if (!$application->getAutoRouting()) {
            $template = $application->routes($this->attributes->getRequestedPage())->getView();
            if ($template) {
                return $template;
            }
        }
        return null;
    }
    
    /**
     * Gets response content type
     *
     * @param Application $application
     * @return string
     */
    private function getContentType(Application $application): string
    {
        $format = $application->formats($this->attributes->getRequestedResponseFormat());
        return $format->getContentType().($format->getCharacterEncoding()?"; charset=".$format->getCharacterEncoding():"");
    }
}
