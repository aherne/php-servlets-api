<?php
namespace Lucinda\STDOUT\EventListeners\Validators;

/**
 * Defines blueprints for request/path parameter value validation
 */
interface ParameterValidator
{
    /**
     * Validates value of a parameter and returns result (eg: matching DB id) or NULL (if validation fails)
     * 
     * @param mixed $value
     * @return mixed|null
     */
    function validate($value);
}

