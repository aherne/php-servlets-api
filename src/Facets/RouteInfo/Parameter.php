<?php

namespace Lucinda\STDOUT\Facets\RouteInfo;

use Lucinda\MVC\XmlReader\Element;
use Lucinda\MVC\XmlReader\Exception;
use Lucinda\STDOUT\EventListeners\Validators\ParameterValidator;

/**
 * Encapsulates information necessary to validate a route/request parameter
 */
class Parameter
{
    private string $name;
    /** @var class-string<ParameterValidator> */
    private string $validator;
    private bool $isMandatory = true;

    /**
     * Saves validation settings from XML tag 'parameter'
     *
     * @param Element $element
     * @throws Exception
     */
    public function __construct(Element $element)
    {
        $attributes = $element->getAttributes();
        $this->setName($attributes);
        $this->setValidator($attributes);
        $this->setMandatory($attributes);
    }

    /**
     * Sets parameter name
     * 
     * @param array<string,string> $attributes
     * @throws Exception If parameter is missing
     */
    private function setName(array $attributes): void
    {
        if (empty($attributes["name"])) {
            throw new Exception("Attribute 'name' is mandatory for 'parameter' tag");
        }
        $this->name = $attributes["name"];
    }

    /**
     * Gets parameter name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets parameter validator
     * 
     * @param array<string,string> $attributes
     * @throws Exception If parameter is missing or class is not instanceof ParameterValidator
     */
    private function setValidator(array $attributes): void
    {
        if (empty($attributes["validator"])) {
            throw new Exception("Attribute 'validator' is mandatory for 'parameter' tag");
        }
        if (!is_subclass_of($attributes["validator"], ParameterValidator::class, true)) {
            throw new Exception("Attribute 'validator' must point to a child of: ".ParameterValidator::class);
        }
        $this->validator = $attributes["validator"];
    }

    /**
     * Gets class that will be used for validating parameter value
     *
     * @return string
     */
    public function getValidator(): string
    {
        return $this->validator;
    }

    /**
     * Sets parameter validator
     * 
     * @param array<string,string> $attributes
     * @throws Exception If parameter is missing or class is not instanceof ParameterValidator
     */
    private function setMandatory(array $attributes): void
    {
        if (isset($attributes["mandatory"]) && $attributes["mandatory"]==="0") {
            $this->isMandatory = false;
        }
    }

    /**
     * Checks whether parameter is mandatory
     *
     * @return boolean
     */
    public function isMandatory(): bool
    {
        return $this->isMandatory;
    }
}
