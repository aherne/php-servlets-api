<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Locates view resolver based on response format name of page requested.
 */
class ViewResolverLocator
{
    private $className;

    /**
     * Locates view resolver on disk based on requested page, response content type and data in XML
     *
     * @param Application $application
     * @param string $format
     * @throws ServletException If view resolver file could not be located on disk.
     */
    public function __construct(Application $application, $format)
    {
        $this->setClassName($application, $format);
    }

    /**
     * Gets resolver class name.
     *
     * @param Application $application
     * @param string $format
     * @throws ServletException If view resolver file could not be located on disk.
     */
    private function setClassName(Application $application, $format)
    {
        // get listener path
        $resolverClass = "";
        $resolverLocation = "";

        // detect resolver @ application
        if ($application->getViewResolversPath()) {
            $format = $application->formats($format);
            $resolverClass = $format->getViewResolver();
            if ($resolverClass) {
                $resolverLocation = $application->getViewResolversPath()."/".$resolverClass.".php";
                if (!file_exists($resolverLocation)) {
                    throw new ServletException("View resolver not found: ".$resolverLocation);
                }
                require($resolverLocation);
            }
        }

        // if no resolver was defined, do nothing
        if (!$resolverLocation) {
            return;
        }

        // validate resolver found or use default
        if (!class_exists($resolverClass)) {
            throw new ServletException("View resolver class not defined: ".$resolverClass);
        }

        $this->className = $resolverClass;

        // checks if it is a subclass of Controller
        if (!is_subclass_of($this->className, __NAMESPACE__."\\"."ViewResolver")) {
            throw new ServletException($this->className." must be a subclass of ViewResolver");
        }
    }

    /**
     * Gets controller class name.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
