<?php
namespace Test\Lucinda\STDOUT\Locators;

use Lucinda\STDOUT\Locators\EventListenerLocator;
use Lucinda\UnitTest\Result;

class EventListenerLocatorTest
{
    public function getClassName()
    {
        $locator = new EventListenerLocator("tests/mocks/events", "StartTracker");
        return new Result($locator->getClassName()=="StartTracker");
    }
}
