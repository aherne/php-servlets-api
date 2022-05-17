<?php

namespace Lucinda\STDOUT\EventListeners;

use Lucinda\MVC\Runnable;
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
    protected Attributes $attributes;
    protected Application $application;
    protected Request $request;
    protected Session $session;
    protected Cookies $cookies;
    protected \Lucinda\MVC\Response $response;

    /**
     * Saves objects to be available in implemented run() methods.
     *
     * @param Attributes $attributes
     * @param Application $application
     * @param Request $request
     * @param Session $session
     * @param Cookies $cookies
     * @param \Lucinda\MVC\Response $response
     */
    public function __construct(
        Attributes $attributes,
        Application $application,
        Request $request,
        Session $session,
        Cookies $cookies,
        \Lucinda\MVC\Response $response
    ) {
        $this->attributes = $attributes;
        $this->application = $application;
        $this->request = $request;
        $this->session = $session;
        $this->cookies = $cookies;
        $this->response = $response;
    }
}
