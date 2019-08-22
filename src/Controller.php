<?php
namespace Lucinda\MVC\STDOUT;

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
     * @var Response
     */
    protected $response;
    
    /**
     * Saves Application, Request and Response objects to be available in implemented run() methods.
     *
     * @param Application $application
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Application $application, Request $request, Response $response)
    {
        $this->application = $application;
        $this->request = $request;
        $this->response = $response;
    }
}
