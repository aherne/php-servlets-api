<?php
/**
 * Listens on response object and alters response body.
 */
abstract class ResponseListener implements Runnable {
	protected $application;
	protected $request;
	protected $response;
	
	/**
	 * @param Application $objApplication
	 * @param Request $objRequest
	 * @param Response $objResponse
	 */
	public function __construct(Application $objApplication, Request $objRequest, Response $objResponse) {
		$this->application = $objApplication;
		$this->request = $objRequest;
		$this->response = $objResponse;
	}
}