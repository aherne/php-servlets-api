<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Encapsulates operations with HTTP response headers.
 */
class ResponseHeaders {
	private $headers;
	
	/**
	 * Gets value of header by name
	 *
	 * @param string $name
	 * @return NULL|string
	 */
	public function get($name) {
		return $this->headers[$name];
	}
	
	/**
	 * Deletes header by name.
	 * 
	 * @param string $name
	 */
	public function delete($name) {
		unset($this->headers[$name]);
	}
	
	/**
	 * Sets value of header by name	 * 
	 * @param string $name
	 * @param string $value
	 */
	public function set($name, $value) {
		$this->headers[$name] = $value;
	}
	
	/**
	 * Checks if header exists by name
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function contains($name) {
		return isset($this->headers[$name]);
	}
	
	/**
	 * Gets all headers
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->headers;
	}
}