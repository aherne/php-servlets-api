<?php
/**
 * Wraps response according to content type. Must be extended by classes implementing type-aware response (html, json).
 */
abstract class Wrapper implements Runnable {
	protected $response;
	
	/**
	 * Constructor performing wrapper's task
	 * @param Response $response
	 */
	public function __construct(Response $response) {
		$this->response = $response;
	}
}