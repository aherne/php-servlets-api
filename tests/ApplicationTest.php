<?php
namespace Test\Lucinda\STDOUT;

use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Objects;
use Test\Lucinda\STDOUT\Support\TestHelper;

class ApplicationTest
{
    private \Lucinda\STDOUT\Application $object;

    public function __construct()
    {
        $this->object = TestHelper::application();
    }

    public function getSessionOptions()
    {
        return new Objects($this->object->getSessionOptions())->assertInstanceOf(
            \Lucinda\STDOUT\XmlTags\SessionOptions::class
        );
    }

    public function getCookieOptions()
    {
        return new Objects($this->object->getCookieOptions())->assertInstanceOf(
            \Lucinda\STDOUT\XmlTags\CookiesOptions::class
        );
    }

    public function getAllRoutes()
    {
        $routes = $this->object->getAllRoutes();
        return [
            (new Arrays($routes))->assertSize(2),
            (new Arrays($routes))->assertContainsKey("users"),
            (new Arrays($routes))->assertContainsKey("user/(name)"),
        ];
    }
}
