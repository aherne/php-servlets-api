<?php
namespace Lucinda\STDOUT\Application;

use Lucinda\MVC\ConfigurationException;
use Lucinda\STDOUT\Application\Route\Parameter;
use Lucinda\STDOUT\Request\Method;

/**
 * Encapsulates extra route information for request validation
 */
class Route extends \Lucinda\MVC\Application\Route
{
    private ?Method $requestMethod = null;
    private array $parameters = [];

    /**
     * Saves response format data detected from XML tag "route".
     *
     * @param \SimpleXMLElement $info
     * @throws ConfigurationException
     */
    public function __construct(\SimpleXMLElement $info)
    {
        parent::__construct($info);
        $this->setValidRequestMethod($info);
        $this->setValidParameters($info);
    }

    /**
     * Sets valid request method for route
     *
     * @param \SimpleXMLElement $info
     * @throws ConfigurationException
     */
    private function setValidRequestMethod(\SimpleXMLElement $info): void
    {
        $method = (string) $info["method"];
        if (!$method) {
            return;
        }
        if ($case = Method::tryFrom(strtoupper($method))) {
            $this->requestMethod = $case;
        } else {
            throw new ConfigurationException("Invalid request method: ".$method);
        }
    }
    
    /**
     * Gets valid request method for current route
     *
     * @return ?Method
     */
    public function getValidRequestMethod(): ?Method
    {
        return $this->requestMethod;
    }

    /**
     * Sets valid route/request parameters for route by name
     *
     * @param \SimpleXMLElement $info
     * @throws ConfigurationException
     */
    private function setValidParameters(\SimpleXMLElement $info): void
    {
        $parameters = $info->xpath("parameter");
        foreach ($parameters as $parameter) {
            $this->parameters[(string) $parameter["name"]] = new Parameter($parameter);
        }
    }
    
    /**
     * Gets validator for route/request parameter by its name for current route
     *
     * @return array
     */
    public function getValidParameters(): array
    {
        return $this->parameters;
    }
}
