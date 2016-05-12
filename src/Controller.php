<?php
/**
 * Defines an abstract controller. 
 */
abstract class Controller implements Runnable {
	/**
	 * @var Application
	 */
	protected $objApplication;
	/**
	 * @var Request
	 */
	protected $objRequest;
	/**
	 * @var Response
	 */
	protected $objResponse;
	
	/**
	 * Implements the three stages of a controller job
	 */
	public function __construct(Application $objApplication, Request $objRequest, Response $objResponse) {
		$this->objApplication = $objApplication;
		$this->objRequest = $objRequest;
		$this->objResponse = $objResponse;
	}
}