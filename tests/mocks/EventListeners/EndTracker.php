<?php

namespace Test\Lucinda\STDOUT\mocks\EventListeners;

use Lucinda\STDOUT\EventListeners\End;

class EndTracker extends End
{
    /**
     * @var \Test\Lucinda\STDOUT\mocks\TestAttributes
     */
    protected \Lucinda\STDOUT\Attributes $attributes;

    public function run(): void
    {
        $this->attributes->setEndTime();
    }
}
