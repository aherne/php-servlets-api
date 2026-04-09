<?php
namespace Test\Lucinda\STDOUT\Validators;

use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;

class ValidationFailedExceptionTest
{
    public function testException()
    {
        $object = new \Lucinda\STDOUT\Validators\ValidationFailedException("invalid parameter");
        return [
            (new Objects($object))->assertInstanceOf(\Lucinda\STDOUT\Validators\ValidationFailedException::class),
            (new Strings($object->getMessage()))->assertEquals("invalid parameter"),
        ];
    }
}
