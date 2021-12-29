<?php
namespace Lucinda\STDOUT\EventListeners;

use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\Attributes;
use Lucinda\STDOUT\Cookies;
use Lucinda\MVC\Runnable;
use Lucinda\STDOUT\Session;

/**
 * Defines blueprint of an event that executes after request that came from client is parsed into a Request object
 */
abstract class Request implements Runnable
{
    protected Attributes $attributes;
    protected Application $application;
    protected \Lucinda\STDOUT\Request $request;
    protected Session $session;
    protected Cookies $cookies;
    
    
    /**
     * Saves objects to be available in implemented run() methods.
     *
     * @param Attributes $attributes
     * @param Application $application
     * @param \Lucinda\STDOUT\Request $request
     * @param Session $session
     * @param Cookies $cookies
     */
    public function __construct(Attributes $attributes, Application $application, \Lucinda\STDOUT\Request $request, Session $session, Cookies $cookies)
    {
        $this->attributes = $attributes;
        $this->application = $application;
        $this->request = $request;
        $this->session = $session;
        $this->cookies = $cookies;
    }
}
