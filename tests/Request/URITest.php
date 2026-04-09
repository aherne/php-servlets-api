<?php

namespace Test\Lucinda\STDOUT\Request;

use Lucinda\STDOUT\Request\URI;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class URITest
{
    private URI $object;

    public function __construct()
    {
        $this->object = new URI(
            TestHelper::requestServer([
                "REQUEST_URI" => "/user/lucian?asd=fgh",
                "QUERY_STRING" => "asd=fgh",
            ])
        );
    }

    public function getContextPath()
    {
        return new Strings($this->object->getContextPath())->assertEquals("");
    }

    public function getPage()
    {
        return new Strings($this->object->getPage())->assertEquals("user/lucian");
    }

    public function getQueryString()
    {
        return new Strings($this->object->getQueryString())->assertEquals("asd=fgh");
    }

    public function parameters()
    {
        return new Arrays($this->object->parameters())->assertEquals(["asd" => "fgh"]);
    }
}
