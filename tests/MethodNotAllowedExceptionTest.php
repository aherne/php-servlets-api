<?php
namespace Test\Lucinda\STDOUT;

use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;

class MethodNotAllowedExceptionTest
{
    public function testException()
    {
        $object = new \Lucinda\STDOUT\MethodNotAllowedException("GET only");
        return [
            (new Objects($object))->assertInstanceOf(\Lucinda\STDOUT\MethodNotAllowedException::class),
            (new Strings($object->getMessage()))->assertEquals("GET only"),
        ];
    }
}
