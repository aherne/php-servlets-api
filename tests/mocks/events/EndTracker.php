<?php
use Lucinda\STDOUT\EventListeners\End;
use Test\Lucinda\STDOUT\TestAttributes;

class EndTracker extends End
{
    /**
     * @var TestAttributes
     */
    protected $attributes;
    
    public function run(): void
    {
        $this->attributes->setEndTime();
    }
}
