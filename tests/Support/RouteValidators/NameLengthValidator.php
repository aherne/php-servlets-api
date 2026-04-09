<?php

namespace Test\Lucinda\STDOUT\Support\RouteValidators;

use Lucinda\STDOUT\Validators\ParameterValidator;

final class NameLengthValidator implements ParameterValidator
{
    public function validate(mixed $value): mixed
    {
        if (!is_string($value) || !preg_match("/^[a-z]+$/i", $value)) {
            return null;
        }

        return strlen($value);
    }
}
