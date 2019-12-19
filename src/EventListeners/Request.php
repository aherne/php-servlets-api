<?php
namespace Lucinda\STDOUT\EventListeners;

use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\Attributes;
use Lucinda\STDOUT\Runnable;

/**
 * Defines blueprint of an event that executes after request that came from client is parsed into a Request object
 */
abstract class Request implements Runnable
{
    /**
     * @var Attributes
     */
    protected $attributes;
    
    /**
     * @var Application
     */
    protected $application;
    
    /**
     * @var \Lucinda\STDOUT\Request
     */
    protected $request;
    
    
    /**
     * Saves objects to be available in implemented run() methods.
     *
     * @param Attributes $attributes
     * @param Application $application
     * @param \Lucinda\STDOUT\Request $request
     */
    public function __construct(Attributes $attributes, Application $application, \Lucinda\STDOUT\Request $request)
    {
        $this->attributes = $attributes;
        $this->application = $application;
        $this->request = $request;
    }
}
