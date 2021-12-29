<?php
namespace Test\Lucinda\STDOUT\mocks\EventListeners;

use Lucinda\STDOUT\EventListeners\Start;

class StartTracker extends Start
{
    /**
     * @var \Test\Lucinda\STDOUT\mocks\TestAttributes
     */
    protected \Lucinda\STDOUT\Attributes $attributes;
    
    public function run(): void
    {
        $this->attributes->setStartTime();
    }
}
