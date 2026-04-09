<?php

namespace Test\Lucinda\STDOUT\Support\RouteValidators;

use Lucinda\STDOUT\Validators\ParameterValidator;

final class PositiveIntegerValidator implements ParameterValidator
{
    public function validate(mixed $value): mixed
    {
        if (!is_numeric($value) || (int) $value <= 0) {
            return null;
        }

        return (int) $value;
    }
}
