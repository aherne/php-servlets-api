<?php
namespace Lucinda\STDOUT\EventListeners;

use Lucinda\STDOUT\Runnable;
use Lucinda\STDOUT\Attributes;

/**
 * Defines blueprint of an event that executes when application starts execution (before XML is read)
 */
abstract class Start implements Runnable
{    
    /**
     * @var Attributes
     */
    protected $attributes;
    
    
    /**
     * Saves objects to be available in implemented run() methods.
     *
     * @param Attributes $attributes
     */
    public function __construct(Attributes $attributes)
    {
        $this->attributes = $attributes;
    }
}