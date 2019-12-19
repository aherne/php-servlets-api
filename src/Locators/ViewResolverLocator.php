<?php
namespace Lucinda\STDOUT\Locators;

use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\Attributes;
use Lucinda\STDOUT\Exception;

/**
 * Locates and loads ViewResolver class based on information collected by Application and Attributes objects
 */
class ViewResolverLocator extends ServiceLocator
{
    /**
     * Triggers service location.
     *
     * @param Application $application
     * @param Attributes $attributes
     * @throws Exception If controller file could not be located on disk.
     */
    public function __construct(Application $application, Attributes $attributes)
    {
        $this->setClassName($application, $attributes);
    }
    
    /**
     * Gets resolver class name.
     *
     * @param Application $application
     * @param Attributes $attributes
     * @throws Exception If view resolver file could not be located on disk.
     */
    protected function setClassName(Application $application, Attributes $attributes): void
    {
        // get listener path
        $resolverClass = $application->formats($attributes->getRequestedResponseFormat())->getViewResolver();
        $resolverLocation = $application->getViewResolversPath();

        // detect resolver @ application
        $classFinder = new ClassFinder($resolverLocation);
        $this->className = $classFinder->find($resolverClass);
    }
}
