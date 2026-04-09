<?php
namespace Test\Lucinda\STDOUT\Validators;

use Lucinda\STDOUT\Validators\FormatValidator;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class FormatValidatorTest
{
    private FormatValidator $object;

    public function __construct()
    {
        $this->object = new FormatValidator(TestHelper::application(), "user/(name)");
    }

    public function getFormat()
    {
        return new Strings($this->object->getFormat())->assertEquals("json");
    }
}
