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
    public function __construct(Application $application, Attributes $attributes): void
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
        $resolverClass = "";
        $resolverLocation = "";

        // detect resolver @ application
        if ($application->getViewResolversPath()) {
            $format = $application->formats($attributes->getRequestedResponseFormat());
            $resolverClass = $format->getViewResolver();
            if ($resolverClass) {
                $resolverLocation = $application->getViewResolversPath()."/".$resolverClass.".php";
                if (!file_exists($resolverLocation)) {
                    throw new Exception("View resolver not found: ".$resolverLocation);
                }
                require_once($resolverLocation);
            }
        }

        // if no resolver was defined, do nothing
        if (!$resolverLocation) {
            return;
        }

        // validate resolver found or use default
        if (!class_exists($resolverClass)) {
            throw new Exception("View resolver class not defined: ".$resolverClass);
        }

        $this->className = $resolverClass;

        // checks if it is a subclass of Controller
        if (!is_subclass_of($this->className, "\\Lucinda\\STDOUT\\ViewResolver")) {
            throw new Exception($this->className." must be a subclass of ViewResolver");
        }
    }
}
