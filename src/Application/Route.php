<?php
namespace Lucinda\STDOUT\Application;

use Lucinda\STDOUT\Application\Route\Parameter;

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
    private $requestMethod;
    private $parameters = [];
    
    /**
     * Saves response format data detected from XML tag "route".
     *
     * @param \SimpleXMLElement $info
     */
    public function __construct(\SimpleXMLElement $info)
    {
        $this->path = (string) $info["url"];
        $this->controllerFile = (string) $info["controller"];
        $this->viewFile = (string) $info["view"];
        $this->format = (string) $info["format"];
        $this->requestMethod = (string) $info["method"];
        $parameters = $info->xpath("parameter");
        foreach ($parameters as $parameter) {
            $this->parameters[(string) $parameter["name"]] = new Parameter($parameter);
        }
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
    public function getController(): ?string
    {
        return $this->controllerFile;
    }
    
    /**
     * Gets view path.
     *
     * @return string
     * @example asd/fgh.html
     */
    public function getView(): ?string
    {
        return $this->viewFile;
    }

    /**
     * Gets response format.
     *
     * @return string
     * @example json
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }
    
    /**
     * Gets valid request method for current route
     *
     * @return string|NULL
     */
    public function getValidRequestMethod(): ?string
    {
        return $this->requestMethod;
    }
    
    /**
     * Gets validator for route/request parameter by its name for current route
     *
     * @param string $name
     * @return Parameter|NULL
     */
    public function getValidParameters(): array
    {
        return $this->parameters;
    }
}
