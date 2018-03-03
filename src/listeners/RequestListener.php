<?php
/**
 * Listens on request object and appends attributes to it.
 */
abstract class RequestListener implements Runnable {
	protected $application;
	protected $request;
	
	/**
	 * @param Application $application
	 * @param Request $request
	 */
	public function __construct(Application $application, Request $request) {
		$this->application = $application;
		$this->request = $request;
	}
}