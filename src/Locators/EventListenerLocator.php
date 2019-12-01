<?php
namespace Lucinda\STDOUT\Locators;

use Lucinda\STDOUT\Exception;

/**
 * Locates and loads EventListener class based on its absolute/relative file path and name
 */
class EventListenerLocator extends ServiceLocator
{
    /**
     * Locates event listener class on disk based on arguments
     *
     * @param string $classPath
     * @param string $className
     */
    public function __construct(string $classPath, string $className): void
    {
        $this->setClassName($classPath, $className);
    }
    
    /**
     * Locates event listener class on disk based on arguments
     *
     * @param string $classPath
     * @param string $className
     * @throws Exception
     */
    private function setClassName(string $classPath, string $className): void
    {
        $classFinder = new ClassFinder($classPath);
        $this->className = $classFinder->find($className);
    }
}
