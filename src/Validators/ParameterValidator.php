<?php

namespace Lucinda\STDOUT\Validators;

/**
 * Defines application-specific validation for route and request parameters
 *
 * Implementations may normalize a valid input into any useful downstream
 * value. Returning null is reserved for validation failure.
 */
interface ParameterValidator
{
    /**
     * Validates and optionally transforms one parameter value
     *
     * @param mixed $value Raw path or request parameter value
     * @return mixed Validated value, or null when validation fails
     */
    public function validate(mixed $value): mixed;
}
