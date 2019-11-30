<?php
namespace Lucinda\STDOUT\Application;

/**
 * Encapsulates route information:
 * - url: relative path requested
 * - cpntroller: path to controller (relative to application/controllers folder)
 * - view: path to view (relative to application/views folder)
 */
class Route
{
    private $path;
    private $controllerFile;
    private $viewFile;
    private $format;
    
    /**
     * Saves response format data detected from XML tag "route".
     *
     * @param string $path
     * @param string $controllerFile
     * @param string $viewFile
     * @param string $format
     */
    public function __construct(string $path, string $controllerFile, string $viewFile, string $format): void
    {
        $this->path = $path;
        $this->controllerFile = $controllerFile;
        $this->viewFile = $viewFile;
        $this->format = $format;
    }
    
    /**
     * Gets route path.
     *
     * @return string
     * @example test/mine		without path parameters
     * @example test/{a}/{b}	with path parameters
     */
    public function getPath(): string
    {
        return $this->path;
    }
    
    /**
     * Gets controller name.
     *
     * @return string
     * @example TestController
     */
    public function getController(): string
    {
        return $this->controllerFile;
    }
    
    /**
     * Gets view path.
     *
     * @return string
     * @example asd/fgh.html
     */
    public function getView(): string
    {
        return $this->viewFile;
    }

    /**
     * Gets response format.
     *
     * @return string
     * @example json
     */
    public function getFormat(): string
    {
        return $this->format;
    }
}
