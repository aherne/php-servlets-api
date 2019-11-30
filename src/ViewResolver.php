<?php
namespace Lucinda\STDOUT;

/**
 * Resolves View into a Response based on Application settings
 */
abstract class ViewResolver implements Runnable
{
    /**
     * @var Application
     */
    protected $application;
    
    /**
     * @var View
     */
    protected $view;
    
    /**
     * @var Response
     */
    protected $response;
    
    /**
     * Saves objects to be available in implemented getContent() methods.
     *
     * @param Application $application
     * @param View $view
     * @param Response $response
     */
    public function __construct(Application $application, View $view, Response $response)
    {
        $this->application = $application;
        $this->view = $view;
        $this->response = $response;
    }
    
    /**
     * Feeds response body with content generated from view by resolver
     */
    public function run()
    {
        $this->response->setBody($this->getContent());
    }
    
    /**
     * Gets body to feed response with
     *
     * @return mixed Content to display: string for html/json/xml response formats.
     */
    abstract protected function getContent();
}
