<?php
/**
 * Listens on request object and appends attributes to it.
 */
abstract class RequestListener implements Runnable {
	protected $application;
	protected $request;
	
	/**
	 * @param Application $objApplication
	 * @param Request $objRequest
	 */
	public function __construct(Application $objApplication, Request $objRequest) {
		$this->application = $objApplication;
		$this->request = $objRequest;
	}
}