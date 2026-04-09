<?php

namespace Test\Lucinda\STDOUT\Request\UploadedFiles;

use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;

class ExceptionTest
{
    public function testException()
    {
        $object = new \Lucinda\STDOUT\Request\UploadedFiles\Exception("upload failed");
        return [
            (new Objects($object))->assertInstanceOf(\Lucinda\STDOUT\Request\UploadedFiles\Exception::class),
            (new Strings($object->getMessage()))->assertEquals("upload failed"),
        ];
    }
}
