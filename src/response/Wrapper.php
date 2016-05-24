<?php
/**
 * Wraps response according to content type. Must be extended by classes implementing type-aware response (html, json).
 */
abstract class Wrapper implements Runnable {
	protected $objResponse;
	
	/**
	 * Constructor performing wrapper's task
	 * @param Response $objResponse
	 */
	public function __construct(Response $objResponse) {
		$this->objResponse = $objResponse;
	}
}