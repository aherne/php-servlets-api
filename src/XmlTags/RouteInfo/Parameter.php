<?php

namespace Lucinda\STDOUT\XmlTags\RouteInfo;

use Lucinda\MVC\XmlReader\Element;
use Lucinda\MVC\XmlReader\Exception;
use Lucinda\STDOUT\Validators\ParameterValidator;

/**
 * Parses one route or request parameter validation rule
 *
 * A parameter requires a name and an autoloadable validator implementing
 * ParameterValidator. Parameters are mandatory unless `mandatory="0"` is
 * configured explicitly.
 */
final class Parameter
{
    /**
     * Parameter name used for lookup and result indexing
     */
    private string $name;

    /**
     * @var class-string<ParameterValidator> Validator instantiated for matching requests
     */
    private string $validator;

    /**
     * Whether absence of the parameter causes validation failure
     */
    private bool $isMandatory = true;

    /**
     * Parses and validates a `parameter` element
     *
     * @param Element $element Parsed `parameter` element
     * @throws Exception If the name or validator is missing or invalid
     */
    public function __construct(Element $element)
    {
        $attributes = $element->getAttributes();
        $this->setName($attributes);
        $this->setValidator($attributes);
        $this->setMandatory($attributes);
    }

    /**
     * Validates and stores the parameter name
     *
     * @param array<string,string> $attributes Parameter attributes
     * @throws Exception If `name` is absent or empty
     */
    private function setName(array $attributes): void
    {
        if (empty($attributes["name"])) {
            throw new Exception("Attribute 'name' is mandatory for 'parameter' tag");
        }
        $this->name = $attributes["name"];
    }

    /**
     * Gets the parameter name
     *
     * @return string Name used for request lookup and validation results
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Validates and stores the parameter validator class
     *
     * @param array<string,string> $attributes Parameter attributes
     * @throws Exception If `validator` is absent or does not implement ParameterValidator
     */
    private function setValidator(array $attributes): void
    {
        if (empty($attributes["validator"])) {
            throw new Exception("Attribute 'validator' is mandatory for 'parameter' tag");
        }
        if (!is_subclass_of($attributes["validator"], ParameterValidator::class)) {
            throw new Exception("Attribute 'validator' must point to a child of: ".ParameterValidator::class);
        }
        $this->validator = $attributes["validator"];
    }

    /**
     * Gets the configured validator class
     *
     * @return class-string<ParameterValidator> Autoloadable validator class name
     */
    public function getValidator(): string
    {
        return $this->validator;
    }

    /**
     * Applies the optional mandatory flag
     *
     * @param array<string,string> $attributes Parameter attributes
     */
    private function setMandatory(array $attributes): void
    {
        if (isset($attributes["mandatory"]) && $attributes["mandatory"]==="0") {
            $this->isMandatory = false;
        }
    }

    /**
     * Checks whether the parameter must be present
     *
     * @return bool True unless `mandatory="0"` was configured
     */
    public function isMandatory(): bool
    {
        return $this->isMandatory;
    }
}
