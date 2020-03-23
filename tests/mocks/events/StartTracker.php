<?php
use Lucinda\STDOUT\EventListeners\Start;
use Test\Lucinda\STDOUT\TestAttributes;

class StartTracker extends Start
{
    /**
     * @var TestAttributes
     */
    protected $attributes;
    
    public function run(): void
    {
        $this->attributes->setStartTime();
    }
}
