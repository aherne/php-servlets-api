<?php
/**
 * Blueprint for class able to process request originally issued by client and extract requested page, path parameters and content type
 */
interface RequestValidator {
	/**
	 * Gets request content type
	 * 
	 * @example application/json
	 * @return string
	 */
	public function getContentType();
	
	/**
	 * Gets requested resource/page
	 * 
	 * @example /asd/def
	 * @return string
	 */
	public function getPage();
	
	/**
	 * Gets path parameters
	 * 
	 * @example array("a","b")
	 * @return array
	 */
	public function getPathParameters();
}