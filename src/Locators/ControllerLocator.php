<?php
namespace Lucinda\STDOUT\Locators;

use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\Attributes;
use Lucinda\STDOUT\Exception;
use Lucinda\STDOUT\XMLException;

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
     * @throws Exception If controller file could not be located on disk.
     * @throws XMLException If XML is misconfigured
     */
    public function __construct(Application $application, Attributes $attributes): void
    {
        $this->setClassName($application, $attributes);
    }
    
    /**
     * Sets controller class name.
     *
     * @param Application $application
     * @param Attributes $attributes
     * @throws Exception If controller file could not be located on disk.
     * @throws XMLException If XML is misconfigured
     */
    protected function setClassName(Application $application, Attributes $attributes): void
    {
        // get controller class folder
        $folder = $application->getControllersPath();
    
        // gets page url
        $url = $attributes->getRequestedPage();
    
        // get controller class name
        $file = "";
        $class = "";
        if (!$application->getAutoRouting()) {
            $path = $application->routes($url)->getController();
            if (!$path) {
                return;
            }
            $file = ($folder?$folder."/":"").$path.".php";
            $slashPosition = strrpos($path, "/");
            if ($slashPosition!==false) {
                $class = substr($path, $slashPosition+1);
                if (!$class) {
                    throw new XMLException("Invalid controller set for route: ".$url);
                }
            } else {
                $class = $path;
            }
        } else {
            $class = str_replace(" ", "", ucwords(str_replace(array("/","-"), " ", strtolower($url))))."Controller";
            $file = $folder."/".$class.".php";
        }
        
        // loads controller file
        if (!file_exists($file)) {
            throw new Exception("Controller not found: ".$class);
        }
        require_once($file);

        // validates and sets controller class
        if (!class_exists($class)) {
            throw new Exception("Controller class not found: ".$class);
        }
        if (!is_subclass_of($class, "\\Lucinda\\STDOUT\\Controller")) {
            throw new Exception($class." must be a subclass of \\Lucinda\\STDOUT\\Controller");
        }
        $this->className = $class;
    }
}
