<?php
namespace Test\Lucinda\STDOUT\Locators;

use Lucinda\STDOUT\Locators\ControllerLocator;
use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\Attributes;
use Lucinda\UnitTest\Result;

class ControllerLocatorTest
{
    public function getClassName()
    {
        $attributes = new Attributes("tests/mocks/events");
        $attributes->setValidPage("users");
        $locator = new ControllerLocator(new Application(dirname(__DIR__)."/configuration.xml"), $attributes);
        return new Result($locator->getClassName()=="UsersController");
    }
}
