<?php
namespace Lucinda\STDOUT;

/**
 * Defines an abstract controller.
 */
abstract class Controller implements Runnable
{
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
     * @var Response
     */
    protected $response;
    
    /**
     * @var Attributes
     */
    protected $attributes;
        
    /**
     * Saves objects to be available in implemented run() methods.
     *
     * @param Attributes $attributes
     * @param Application $application
     * @param Request $request
     * @param Session $session
     * @param Cookies $cookies
     * @param Response $response
     */
    public function __construct(Attributes $attributes, Application $application, Request $request, Session $session, Cookies $cookies, Response $response)
    {
        $this->attributes = $attributes;
        $this->application = $application;
        $this->request = $request;
        $this->session = $session;
        $this->cookies = $cookies;
        $this->response = $response;
    }
}
