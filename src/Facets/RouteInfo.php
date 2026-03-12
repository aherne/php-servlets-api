<?php

namespace Lucinda\STDOUT\Facets;

use Lucinda\MVC\Facets\RouteInfo as AbstractRouteInfo;
use Lucinda\MVC\XmlReader\Element;
use Lucinda\MVC\XmlReader\Exception;
use Lucinda\STDOUT\Controller;
use Lucinda\STDOUT\Facets\RouteInfo\Parameter;
use Lucinda\STDOUT\Request\Method;

/**
 * Encapsulates extra route information for request validation
 */
final class RouteInfo extends AbstractRouteInfo
{
    private ?Method $requestMethod = null;
    /**
     * @var array<string,string>
     */
    private array $parameters = [];

    /**
     * Saves response format data detected from XML tag "route".
     *
     * @param  Element $element
     * @throws Exception
     */
    public function __construct(Element $element)
    {
        parent::__construct($element);
        $this->setValidRequestMethod($element);
        $this->setValidParameters($element);
    }

    /**
     * Sets valid request method for route
     *
     * @param  Element $element
     * @throws Exception
     */
    private function setValidRequestMethod(Element $element): void
    {
        $attributes = $element->getAttributes();
        if (empty($attributes["method"])) {
            return;
        }
        if ($case = Method::tryFrom(strtoupper($attributes["method"]))) {
            $this->requestMethod = $case;
        } else {
            throw new Exception("Invalid request method: ".$attributes["method"]);
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
     * @param  Element $element
     * @throws ConfigurationException
     */
    private function setValidParameters(Element $element): void
    {
        $childen = $element->getChildren();
        if (empty($childen) || !isset($childen["parameter"])) {
            return;
        }
        foreach ($childen["parameter"] as $parameter) {
            $info = new Parameter($parameter);
            $this->parameters[$info->getName()] = $info;
        }
    }

    /**
     * Gets validator for route/request parameter by its name for current route
     *
     * @return array<string,string>
     */
    public function getValidParameters(): array
    {
        return $this->parameters;
    }
}
