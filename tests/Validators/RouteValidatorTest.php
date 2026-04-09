<?php
namespace Test\Lucinda\STDOUT\Validators;

use Lucinda\STDOUT\Validators\RouteValidator;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class RouteValidatorTest
{
    private RouteValidator $object;

    public function __construct()
    {
        $request = TestHelper::request(
            TestHelper::requestServer([
                "REQUEST_URI" => "/user/lucian?page=3",
                "QUERY_STRING" => "page=3",
            ]),
            ["page" => "3"]
        );
        $this->object = new RouteValidator(TestHelper::application(), $request);
    }

    public function getUrl()
    {
        return new Strings($this->object->getUrl())->assertEquals("user/(name)");
    }

    public function getPathParameters()
    {
        return new Arrays($this->object->getPathParameters())->assertEquals(["name" => "lucian"]);
    }

    public function getValidParameters()
    {
        return new Arrays($this->object->getValidParameters())->assertEquals([
            "name" => 6,
            "page" => 3,
        ]);
    }
}
