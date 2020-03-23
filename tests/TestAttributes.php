<?php
namespace Test\Lucinda\STDOUT;

use Lucinda\STDOUT\Attributes;

class TestAttributes extends Attributes
{
    private $startTime;
    private $endTime;
    
    public function setStartTime(): void
    {
        $this->startTime = microtime(true);
    }
    
    public function getStartTime(): float
    {
        return $this->startTime;
    }
    
    public function setEndTime(): void
    {
        $this->endTime = microtime(true);
    }
    
    public function getEndTime(): float
    {
        return $this->endTime;
    }
}
