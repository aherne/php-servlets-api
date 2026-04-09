<?php

namespace Test\Lucinda\STDOUT\XmlTags\RouteInfo;

use Lucinda\STDOUT\XmlTags\RouteInfo\Parameter;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class ParameterTest
{
    private Parameter $object;

    public function __construct()
    {
        $this->object = new Parameter(
            TestHelper::element(
                '<parameter name="page" validator="Test\Lucinda\STDOUT\Support\RouteValidators\PositiveIntegerValidator" mandatory="0"/>'
            )
        );
    }

    public function getName()
    {
        return new Strings($this->object->getName())->assertEquals("page");
    }

    public function getValidator()
    {
        return new Strings($this->object->getValidator())->assertEquals(
            \Test\Lucinda\STDOUT\Support\RouteValidators\PositiveIntegerValidator::class
        );
    }

    public function isMandatory()
    {
        return new Booleans($this->object->isMandatory())->assertFalse();
    }
}
