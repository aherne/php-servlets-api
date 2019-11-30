<?php
namespace Lucinda\STDOUT\EventListeners;

use Lucinda\STDOUT\Runnable;
use Lucinda\STDOUT\Attributes;
use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\Request;

/**
 * Defines blueprint of an event that executes after Session object is created
 */
abstract class Session implements Runnable
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
     * @var Request
     */
    protected $request;
    
    /**
     * @var \Lucinda\STDOUT\Session
     */
    protected $session;
    
    
    /**
     * Saves objects to be available in implemented run() methods.
     *
     * @param Attributes $attributes
     * @param Application $application
     * @param Request $request
     * @param \Lucinda\STDOUT\Session $session
     */
    public function __construct(Attributes $attributes, Application $application, Request $request, Session $session)
    {
        $this->attributes = $attributes;
        $this->application = $application;
        $this->request = $request;
        $this->session = $session;
    }
}
