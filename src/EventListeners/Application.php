<?php
namespace Lucinda\STDOUT\EventListeners;

use Lucinda\STDOUT\Attributes;
use Lucinda\MVC\Runnable;

/**
 * Defines blueprint of an event that executes after XML that contains application settings is parsed
 */
abstract class Application implements Runnable
{
    protected Attributes $attributes;
    protected \Lucinda\STDOUT\Application $application;
    
    
    /**
     * Saves objects to be available in implemented run() methods.
     *
     * @param Attributes $attributes
     * @param \Lucinda\STDOUT\Application $application
     */
    public function __construct(Attributes $attributes, \Lucinda\STDOUT\Application $application)
    {
        $this->application = $application;
        $this->attributes = $attributes;
    }
}
