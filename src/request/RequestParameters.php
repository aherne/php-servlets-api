<?php
/**
 * Wrapper over request headers/get/post parameters that enforces immutability as well as object-oriented access
 */
class RequestParameters {
	private $parameters;
	
	/**
	 * Saves a local immutable copy of input parameters.
	 * 
	 * @param array $parameters
	 */
	public function __construct($parameters) {
		$this->parameters = $parameters;
	}
	
	/**
	 * Gets value of parameter by key
	 * 
	 * @param string $name
	 * @return NULL|mixed
	 */
	public function get($name) {
		if(!isset($this->parameters[$name])) return null;
		return $this->parameters[$name];
	}
	
	/**
	 * Checks if parameter exists by key
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function contains($name) {
		return isset($this->parameters[$name]);
	}
	
	/**
	 * Gets all parameters
	 * 
	 * @return array
	 */
	public function getAll() {
		return $this->parameters;
	}
}