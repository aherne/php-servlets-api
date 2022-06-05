<?php

namespace Lucinda\STDOUT\EventListeners;

use Lucinda\MVC\Runnable;
use Lucinda\STDOUT\Attributes;
use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\Request;
use Lucinda\STDOUT\Session;
use Lucinda\STDOUT\Cookies;
use Lucinda\MVC\Response;

/**
 * Defines blueprint of an event that executes when application ends execution (after response is committed to client)
 */
abstract class End implements Runnable
{
    protected Attributes $attributes;
    protected Application $application;
    protected Request $request;
    protected Session $session;
    protected Cookies $cookies;
    protected Response $response;

    /**
     * Saves objects to be available in implemented run() methods.
     *
     * @param Attributes  $attributes
     * @param Application $application
     * @param Request     $request
     * @param Session     $session
     * @param Cookies     $cookies
     * @param Response    $response
     */
    public function __construct(
        Attributes $attributes,
        Application $application,
        Request $request,
        Session $session,
        Cookies $cookies,
        Response $response
    ) {
        $this->attributes = $attributes;
        $this->application = $application;
        $this->request = $request;
        $this->session = $session;
        $this->cookies = $cookies;
        $this->response = $response;
    }
}
