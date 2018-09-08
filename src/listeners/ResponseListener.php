<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Listens on response object and alters response body.
 */
abstract class ResponseListener implements Runnable {
	protected $application;
	protected $request;
	protected $response;
	
	/**
	 * @param Application $application
	 * @param Request $request
	 * @param Response $response
	 */
	public function __construct(Application $application, Request $request, Response $response) {
		$this->application = $application;
		$this->request = $request;
		$this->response = $response;
	}
}