<?php
/**
 * Listens on request object and appends attributes to it.
 */
abstract class RequestListener implements Runnable {
	protected $objApplication;
	protected $objRequest;
	
	/**
	 * @param Application $objApplication
	 * @param Request $objRequest
	 */
	public function __construct(Application $objApplication, Request $objRequest) {
		$this->objApplication = $objApplication;
		$this->objRequest = $objRequest;
	}
}