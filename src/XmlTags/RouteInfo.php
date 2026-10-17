<?php

namespace Lucinda\STDOUT\XmlTags;

use Lucinda\MVC\XmlTags\RouteInfo as AbstractRouteInfo;
use Lucinda\MVC\XmlReader\Element;
use Lucinda\MVC\XmlReader\Exception;
use Lucinda\STDOUT\XmlTags\RouteInfo\Parameter;
use Lucinda\STDOUT\Request\Method;

/**
 * Extends MVC route metadata with HTTP method and parameter validation rules
 *
 * The optional `method` attribute is normalized to a Request\Method case. Each
 * child `parameter` element becomes a Parameter keyed by its configured name.
 */
final class RouteInfo extends AbstractRouteInfo
{
    /**
     * Required request method, or null when the route accepts every method
     */
    private ?Method $requestMethod = null;

    /**
     * @var array<string,Parameter> Parameter validation rules keyed by parameter name
     */
    private array $parameters = [];

    /**
     * Parses base route metadata and HTTP-specific validation rules
     *
     * @param Element $element Parsed `route` element
     * @throws Exception If the method or a parameter definition is invalid
     */
    public function __construct(Element $element)
    {
        parent::__construct($element);
        $this->setValidRequestMethod($element);
        $this->setValidParameters($element);
    }

    /**
     * Parses the optional request method constraint
     *
     * @param Element $element Parsed `route` element
     * @throws Exception If `method` is not a supported Request\Method value
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
     * Gets the request method accepted by this route
     *
     * @return Method|null Required method, or null when every method is accepted
     */
    public function getValidRequestMethod(): ?Method
    {
        return $this->requestMethod;
    }

    /**
     * Parses child parameter validation rules
     *
     * @param Element $element Parsed `route` element
     * @throws Exception If any parameter definition is invalid
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
     * Gets all parameter validation rules for this route
     *
     * @return array<string,Parameter> Rules keyed by parameter name
     */
    public function getValidParameters(): array
    {
        return $this->parameters;
    }
}
