<?php
namespace Lucinda\MVC\STDOUT;

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
	 * Gets path parameters detected by optional name
	 *
	 * @param string $name
	 * @return string[string]|NULL|string
	 */
	public function parameters($name="");
}