<?php
namespace Lucinda\MVC\STDOUT;

require_once("ClassFinder.php");

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
        $resolverClass = $application->formats($format)->getViewResolver();
        $resolverLocation = $application->getViewResolversPath();
        $classFinder = new ClassFinder($application->getViewResolversPath());
        $this->className = $classFinder->find($application->formats($format)->getViewResolver());
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
