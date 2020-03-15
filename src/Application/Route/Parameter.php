<?php
namespace Lucinda\STDOUT\Application\Route;

use Lucinda\STDOUT\XMLException;

/**
 * Encapsulates information necessary to validate a route/request parameter
 */
class Parameter
{
    private $name;
    private $validator;
    private $isMandatory = true;
    
    /**
     * Saves validation settings from XML tag 'parameter'
     * 
     * @param \SimpleXMLElement $info
     * @throws XMLException
     */
    public function __construct(\SimpleXMLElement $info)
    {
        $this->name = (string) $info["name"];
        if (!$this->name) {
            throw new XMLException("Attribute 'name' of tag 'parameter' is mandatory");
        }
        
        $this->validator = (string) $info["validator"];
        if (!$this->validator) {
            throw new XMLException("Attribute 'validator' of tag 'parameter' is mandatory");
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
     * Checks whether or not parameter is mandatory
     * 
     * @return boolean
     */
    public function isMandatory(): bool
    {
        return $this->isMandatory;
    }
}

