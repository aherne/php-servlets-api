<?php
namespace Lucinda\STDOUT\EventListeners;

use Lucinda\STDOUT\Runnable;
use Lucinda\STDOUT\Attributes;
use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\Request;
use Lucinda\STDOUT\Session;
use Lucinda\STDOUT\Cookies;

/**
 * Defines blueprint of an event that executes before response is rendered to client
 */
abstract class Response implements Runnable
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
     * @var Cookies
     */
    protected $cookies;
    
    /**
     * @var \Lucinda\STDOUT\Response
     */
    protected $response;
    
    
    /**
     * Saves objects to be available in implemented run() methods.
     *
     * @param Attributes $attributes
     * @param Application $application
     * @param Request $request
     * @param Session $session
     * @param Cookies $cookies
     * @param \Lucinda\STDOUT\Response $response
     */
    public function __construct(Attributes $attributes, Application $application, Request $request, Session $session, Cookies $cookies, \Lucinda\STDOUT\Response $response)
    {
        $this->attributes = $attributes;
        $this->application = $application;
        $this->request = $request;
        $this->session = $session;
        $this->cookies = $cookies;
        $this->response = $response;
    }
}
