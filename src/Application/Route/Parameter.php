<?php
namespace Lucinda\STDOUT\Application\Route;

use Lucinda\MVC\ConfigurationException;

/**
 * Encapsulates information necessary to validate a route/request parameter
 */
class Parameter
{
    private string $name;
    private string $validator;
    private bool $isMandatory = true;
    
    /**
     * Saves validation settings from XML tag 'parameter'
     *
     * @param \SimpleXMLElement $info
     * @throws ConfigurationException
     */
    public function __construct(\SimpleXMLElement $info)
    {
        $this->name = (string) $info["name"];
        if (!$this->name) {
            throw new ConfigurationException("Attribute 'name' is mandatory for 'parameter' tag");
        }
        
        $this->validator = (string) $info["validator"];
        if (!$this->validator) {
            throw new ConfigurationException("Attribute 'validator' is mandatory for 'parameter' tag");
        }
        
        $mandatory = (string) $info["mandatory"];
        if ($mandatory==="0") {
            $this->isMandatory = false;
        }
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
     * Gets class that will be used for validating parameter value
     *
     * @return string
     */
    public function getValidator(): string
    {
        return $this->validator;
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
