<?php

namespace Test\Lucinda\STDOUT\XmlTags;

use Lucinda\STDOUT\XmlTags\RouteInfo;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class RouteInfoTest
{
    private RouteInfo $object;

    public function __construct()
    {
        $this->object = new RouteInfo(
            TestHelper::element(
                '<route id="user/(name)" controller="Test\Lucinda\STDOUT\Support\Controllers\UserJsonController" format="json" method="GET"><parameter name="name" validator="Test\Lucinda\STDOUT\Support\RouteValidators\NameLengthValidator"/></route>'
            )
        );
    }

    public function getValidRequestMethod()
    {
        return new Strings($this->object->getValidRequestMethod()->value)->assertEquals("GET");
    }

    public function getValidParameters()
    {
        return new Arrays($this->object->getValidParameters())->assertContainsKey("name");
    }
}
