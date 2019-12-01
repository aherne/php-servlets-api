<?php
namespace Lucinda\MVC\STDOUT;

require_once("ClassFinder.php");

/**
 * Locates all event listeners based on data in XML tag <listeners>.
 */
class ListenerLocator
{
    private $classNames = array();
    
    /**
     * Detects all event listeners based on entries in XML tag <listeners>.
     *
     * @param Application $application
     * @throws ServletException If listener file could not be located on disk.
     */
    public function __construct(Application $application)
    {
        $this->setClassNames($application);
    }

    /**
     * Locates listeners by component name (configuration | request | response).
     *
     * @param Application $application
     * @throws ServletException If listener file could not be located on disk.
     */
    private function setClassNames(Application $application)
    {
        
        // gets classes
        $output = array();
        $classFinder = new ClassFinder($application->getListenersPath());
        $listeners = $application->getListeners();
        foreach ($listeners as $className) {
            $output[] = $classFinder->find($className);
        }
        
        $this->classNames = $output;
    }
    
    /**
     * Gets event listener class names by parent class name.
     *
     * @param string $parentClassName
     * @return string[]
     */
    public function getClassNames($parentClassName)
    {
        $output = array();
        foreach ($this->classNames as $className) {
            if (is_subclass_of($className, __NAMESPACE__."\\".$parentClassName)) {
                $output[] = $className;
            }
        }
        return $output;
    }
}
