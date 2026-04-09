<?php
namespace Test\Lucinda\STDOUT;

use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;

class PathNotFoundExceptionTest
{
    public function testException()
    {
        $object = new \Lucinda\STDOUT\PathNotFoundException("missing route");
        return [
            (new Objects($object))->assertInstanceOf(\Lucinda\STDOUT\PathNotFoundException::class),
            (new Strings($object->getMessage()))->assertEquals("missing route"),
        ];
    }
}
