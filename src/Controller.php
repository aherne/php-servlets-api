<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Defines an abstract controller. 
 */
abstract class Controller implements Runnable {
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
	 * Implements the three stages of a controller job
	 */
	public function __construct(Application $application, Request $request, Response $response) {
		$this->application = $application;
		$this->request = $request;
		$this->response = $response;
	}
}