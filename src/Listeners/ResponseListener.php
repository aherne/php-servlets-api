<?php
/**
 * Listens on response object and alters response body.
 */
abstract class ResponseListener implements Runnable {
	protected $objApplication;
	protected $objRequest;
	protected $objResponse;
	
	/**
	 * @param Application $objApplication
	 * @param Request $objRequest
	 * @param Response $objResponse
	 */
	public function __construct(Application $objApplication, Request $objRequest, Response $objResponse) {
		$this->objApplication = $objApplication;
		$this->objRequest = $objRequest;
		$this->objResponse = $objResponse;
	}
}