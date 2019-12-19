<?php
namespace Lucinda\STDOUT\EventListeners;

use Lucinda\STDOUT\Runnable;
use Lucinda\STDOUT\Attributes;
use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\Request;
use Lucinda\STDOUT\Session;
use Lucinda\STDOUT\Response;

/**
 * Defines blueprint of an event that executes after Cookies object is created
 */
abstract class Cookies implements Runnable
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
     * @var Session
     */
    protected $session;
    
    /**
     * @var \Lucinda\STDOUT\Cookies
     */
    protected $cookies;
    
    
    /**
     * Saves objects to be available in implemented run() methods.
     *
     * @param Attributes $attributes
     * @param Application $application
     * @param Request $request
     * @param Session $session
     * @param \Lucinda\STDOUT\Cookies $cookies
     * @param Response $response
     */
    public function __construct(Attributes $attributes, Application $application, Request $request, Session $session, \Lucinda\STDOUT\Cookies $cookies)
    {
        $this->attributes = $attributes;
        $this->application = $application;
        $this->request = $request;
        $this->session = $session;
        $this->cookies = $cookies;
    }
}
