<?php
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
	public function __construct(Application $objApplication, Request $objRequest, Response $objResponse) {
		$this->application = $objApplication;
		$this->request = $objRequest;
		$this->response = $objResponse;
	}
}