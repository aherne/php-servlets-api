<?php
/**
 * Manages operations with response stream, in which response is aggregated before it is committed.
 */
final class ResponseStream {
	/**
	 * Stores whatever is pending display before response is committed. Can be:
	 * - string content (eg: html, xml, json)
	 * - binary content (eg: jpeg)
	 * 
	 * @var string $contents
	 */
	private $contents;
	
	/**
	 * Returns information saved to stream.
	 * 
	 * @return string
	 */
	public function get() {
		return $this->contents;
	}
	
	/**
	 * Clears stream
	 * 
	 * @return void
	 */
	public function clear() {
		$this->contents = null;
	}
	
	/**
	 * Writes to stream.
	 * 
	 * @param string $contents
	 */
	public function write($contents) {
		$this->contents .= $contents;
	}
	
	/**
	 * Sets stream directly.
	 * 
	 * @param string $contents
	 */
	public function set($contents) {
		$this->contents = $contents;
	}
	
	/**
	 * Checks if stream is empty.
	 */
	public function isEmpty() {
	    	return empty($this->contents);
	}
}
