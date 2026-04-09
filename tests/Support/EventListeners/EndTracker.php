<?php

namespace Test\Lucinda\STDOUT\Support\EventListeners;

use Lucinda\MVC\EventListener;
use Test\Lucinda\STDOUT\Support\TestHelper;

final class EndTracker implements EventListener
{
    public function run(): void
    {
        TestHelper::logEvent("end");
    }
}
