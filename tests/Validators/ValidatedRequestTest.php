<?php
namespace Test\Lucinda\STDOUT\Validators;

use Lucinda\STDOUT\Validators\ValidatedRequest;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class ValidatedRequestTest
{
    private ValidatedRequest $object;

    public function __construct()
    {
        $request = TestHelper::request(
            TestHelper::requestServer([
                "REQUEST_URI" => "/user/lucian?page=3",
                "QUERY_STRING" => "page=3",
            ]),
            ["page" => "3"]
        );
        $this->object = new ValidatedRequest(TestHelper::application(), $request);
    }

    public function getRoute()
    {
        return new Strings($this->object->getRoute())->assertEquals("user/(name)");
    }

    public function getFormat()
    {
        return new Strings($this->object->getFormat())->assertEquals("json");
    }

    public function getPathParameters()
    {
        return new Arrays($this->object->getPathParameters())->assertEquals(["name" => "lucian"]);
    }

    public function getValidationResults()
    {
        return new Arrays($this->object->getValidationResults())->assertEquals([
            "name" => 6,
            "page" => 3,
        ]);
    }
}
