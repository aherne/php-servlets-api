<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Resolves response into output stream according to content type. Must be extended by classes implementing type-aware response (html, json).
 */
abstract class ViewResolver implements Runnable {
    protected $application;
	protected $response;
	
	/**
	 * Constructor performing resolver task
	 * @param Response $response
	 */
	public function __construct(Application $application, Response $response) {
	    $this->application = $application;
		$this->response = $response;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Lucinda\MVC\STDOUT\Runnable::run()
	 */
	public function run() {
	    $this->response->getOutputStream()->write($this->getContent());
	}
	
	/**
	 * Gets view content to write to output stream
	 * 
	 * @return mixed A string for html/json/xml response formats.
	 */
	abstract protected function getContent();
}