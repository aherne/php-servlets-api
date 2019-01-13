<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Listens on request object and appends attributes to it.
 */
abstract class RequestListener implements Runnable {
	protected $application;
	protected $request;
	
	/**
	 * Saves Application & Request objects to be available in implemented run() methods.
	 * 
	 * @param Application $application
	 * @param Request $request
	 */
	public function __construct(Application $application, Request $request) {
		$this->application = $application;
		$this->request = $request;
	}
}