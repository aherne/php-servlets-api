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
    public function __construct(string $documentDescriptor, Attributes $attributes)
    {
        // saves arguments
        $this->documentDescriptor = $documentDescriptor;
        $this->attributes = $attributes;
        
        // initialize events
        $this->events = [
            EventType::START=>[],
            EventType::APPLICATION=>[],
            EventType::REQUEST=>["\\Lucinda\\STDOUT\\EventListeners\\RequestValidator"=>__DIR__."/EventListeners"],
            EventType::RESPONSE=>[],
            EventType::END=>[]
        ];
    }
    
    /**
     * Adds an event listener
     *
     * @param string $type One of EventType enum values
     * @param string $className Name of event listener class (including namespace and subfolder, if any)
     */
    public function addEventListener(string $type, string $className): void
    {
        $this->events[$type][$className]=$this->attributes->getEventsFolder();
    }
    
    /**
     * Performs all steps required to convert request to response in procedural mode, while delegating to subcomponents, to maximize performance
     *
     * @throws PathNotFoundException If an invalid route was requested from client or setup by developer in XML.
     * @throws FileUploadException If file upload failed due to server constraints.
     * @throws ConfigurationException If any other situation where execution cannot continue.
     */
    public function run(): void
    {
        // execute events for START
        foreach ($this->events[EventType::START] as $class=>$path) {
            $eventLocator = new EventListenerLocator($path, $class);
            $className = $eventLocator->getClassName();
            $runnable = new $className($this->attributes);
            $runnable->run();
        }
        
        // reads XML configuration file
        $application = new Application($this->documentDescriptor);
        
        // execute events for APPLICATION
        foreach ($this->events[EventType::APPLICATION] as $class=>$path) {
            $eventLocator = new EventListenerLocator($path, $class);
            $className = $eventLocator->getClassName();
            $runnable = new $className($this->attributes, $application);
            $runnable->run();
        }
        
        // reads user request, into request (RO), session (RW) and cookies (RW) objects
        $request = new Request();
        $session = new Session($application->getSessionOptions());
        $cookies = new Cookies($application->getCookieOptions());
        
        // execute events for REQUEST
        foreach ($this->events[EventType::REQUEST] as $class=>$path) {
            $eventLocator = new EventListenerLocator($path, $class);
            $className = $eventLocator->getClassName();
            $runnable = new $className($this->attributes, $application, $request, $session, $cookies);
            $runnable->run();
        }
                
        // initializes response
        $response = new Response($this->getContentType($application), $this->getTemplateFile($application));

        // locates and runs page controller
        $controllerLocator = new ControllerLocator($application, $this->attributes);
        $className  = $controllerLocator->getClassName();
        if ($className) {
            $runnable = new $className($this->attributes, $application, $request, $session, $cookies, $response);
            $runnable->run();
        }

        // resolves view into response body, unless output stream has been written to already
        if ($response->getBody()===null) {
            $viewResolverLocator = new ViewResolverLocator($application, $this->attributes);
            $className  = $viewResolverLocator->getClassName();
            if ($className) {
                $runnable = new $className($application, $response);
                $runnable->run();
            }
        }
        
        // execute events for RESPONSE
        foreach ($this->events[EventType::RESPONSE] as $class=>$path) {
            $eventLocator = new EventListenerLocator($path, $class);
            $className = $eventLocator->getClassName();
            $runnable = new $className($this->attributes, $application, $request, $session, $cookies, $response);
            $runnable->run();
        }

        // commits response to caller
        $response->commit();
        
        // execute events for END
        foreach ($this->events[EventType::END] as $class=>$path) {
            $eventLocator = new EventListenerLocator($path, $class);
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
            $template = $application->routes($this->attributes->getValidPage())->getView();
            return ($template?$application->getViewsPath()."/".$template:"");
        } else {
            return $application->getViewsPath()."/".$this->attributes->getValidPage();
        }
    }
    
    /**
     * Gets response content type
     *
     * @param Application $application
     * @return string
     */
    private function getContentType(Application $application): string
    {
        $format = $application->formats($this->attributes->getValidFormat());
        return $format->getContentType().($format->getCharacterEncoding()?"; charset=".$format->getCharacterEncoding():"");
    }
}
