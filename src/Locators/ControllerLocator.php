<?php
namespace Lucinda\STDOUT\Locators;

use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\Attributes;
use Lucinda\MVC\ConfigurationException;
use Lucinda\MVC\Locators\ServiceLocator;
use Lucinda\MVC\Locators\ClassFinder;

/**
 * Locates and loads Controller class based on information collected by Application and Attributes objects
 */
class ControllerLocator extends ServiceLocator
{
    /**
     * Triggers service location.
     *
     * @param Application $application
     * @param Attributes $attributes
     * @throws ConfigurationException If controller file could not be located on disk.
     */
    public function __construct(Application $application, Attributes $attributes)
    {
        $this->setClassName($application, $attributes);
    }
    
    /**
     * Sets controller class name.
     *
     * @param Application $application
     * @param Attributes $attributes
     * @throws ConfigurationException If controller file could not be located on disk.
     */
    protected function setClassName(Application $application, Attributes $attributes): void
    {
        // get controller class folder
        $folder = $application->getControllersPath();
    
        // gets page url
        $url = $attributes->getValidPage();
    
        // get controller class name
        $className = "";
        if (!$application->getAutoRouting()) {
            $className = $application->routes($url)->getController();
            
            // it is ok not to have a controller
            if (!$className) {
                return;
            }
        } else {
            $className = str_replace(" ", "", ucwords(str_replace(array("/","-"), " ", strtolower($url))))."Controller";
        }
        
        
        // loads and locates class
        $classFinder = new ClassFinder($folder);
        $this->className = $classFinder->find($className);
    }
}
