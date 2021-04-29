<?php
namespace Lucinda\STDOUT\Application;

use Lucinda\STDOUT\Application\Route\Parameter;

/**
 * Encapsulates route information:
 * - url: relative path requested
 * - cpntroller: path to controller (relative to application/controllers folder)
 * - view: path to view (relative to application/views folder)
 */
class Route extends \Lucinda\MVC\Application\Route
{
    private $requestMethod;
    private $parameters = [];
    
    /**
     * Saves response format data detected from XML tag "route".
     *
     * @param \SimpleXMLElement $info
     */
    public function __construct(\SimpleXMLElement $info)
    {
        parent::__construct($info);
        $this->requestMethod = (string) $info["method"];
        $parameters = $info->xpath("parameter");
        foreach ($parameters as $parameter) {
            $this->parameters[(string) $parameter["name"]] = new Parameter($parameter);
        }
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
