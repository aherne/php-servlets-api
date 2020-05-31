<?php
namespace Test\Lucinda\STDOUT\Locators;

use Lucinda\STDOUT\Locators\ViewResolverLocator;
use Lucinda\STDOUT\Application;
use Lucinda\UnitTest\Result;
use Lucinda\STDOUT\Attributes;

class ViewResolverLocatorTest
{
    public function getClassName()
    {
        $attributes = new Attributes("tests/mocks/events");
        $attributes->setValidFormat("html");
        $locator = new ViewResolverLocator(new Application(dirname(__DIR__)."/configuration.xml"), $attributes);
        return new Result($locator->getClassName()=="HtmlResolver");
    }
}
