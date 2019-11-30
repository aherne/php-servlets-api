<?php
namespace Lucinda\STDOUT\Locators;

use Lucinda\STDOUT\Exception;

/**
 * Locates and loads EventListener class based on its absolute/relative file path and namespace
 */
class EventListenerLocator
{
    /**
     * Locates event listener class on disk based on arguments
     * 
     * @param string $classPath
     * @param string $namespace
     * @param string $extends
     */
    public function __construct($classPath, $namespace, $extends)
    {
        $this->setClassName($classPath, $namespace, $extends);
    }
    
    /**
     * Locates event listener class on disk based on arguments
     * 
     * @param string $classPath
     * @param string $namespace
     * @param string $extends
     * @throws Exception
     */
    private function setClassName($classPath, $namespace, $extends)
    {
        $className = $namespace."\\".substr($classPath, strrpos($classPath, "/")+1);
        
        // loads event class
        if (!file_exists($classPath.".php")) {
            throw new Exception("Event listener not found: ".$className);
        }
        require_once($classPath.".php");
        
        // validates and sets controller class
        if (!class_exists($className)) {
            throw new Exception("Class not found: ".$className);
        }
        if (!is_subclass_of($className, $extends)) {
            throw new Exception($className." must be a subclass of ".$extends);
        }
        $this->className = $className;        
    }
    
    /**
     * Gets event listener class name.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
