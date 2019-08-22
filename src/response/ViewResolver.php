<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Resolves response into output stream according to content type. Must be extended by classes implementing type-aware response (html, json).
 */
abstract class ViewResolver implements Runnable
{
    protected $application;
    protected $response;
    
    /**
     * Saves Application and Response objects to be available in implemented getContent() methods.
     *
     * @param Application $application
     * @param Response $response
     */
    public function __construct(Application $application, Response $response)
    {
        $this->application = $application;
        $this->response = $response;
    }
    
    /**
     * {@inheritDoc}
     * @see Runnable::run()
     */
    public function run()
    {
        $this->response->getOutputStream()->write($this->getContent());
    }
    
    /**
     * Gets view content to write to output stream
     *
     * @return mixed Content to display: string for html/json/xml response formats.
     */
    abstract protected function getContent();
}
