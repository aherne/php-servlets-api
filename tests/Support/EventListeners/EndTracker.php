<?php

namespace Test\Lucinda\STDOUT\Support\EventListeners;

use Lucinda\MVC\EventListener;
use Lucinda\MVC\EventListener\UnFaceted;
use Test\Lucinda\STDOUT\Support\TestHelper;

final class EndTracker implements UnFaceted
{
    public function run(): void
    {
        TestHelper::logEvent("end");
    }
}
