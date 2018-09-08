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
	 * Gets value of path parameter by name
	 *
	 * @example Henry @ /asd/def/(name) that mapped request to /asd/def/Henry
	 * @param string $name
	 * @return string|null Null if not exists, string otherwise.
	 */
	public function getPathParameter($name);
	
	/**
	 * Gets all path parameters
	 * 
	 * @example array("id"=>"234","name"=>"Henry") @ /asd/def/(id)/(name) that mapped request to /asd/def/234/Henry
	 * @return array[string:string]
	 */
	public function getPathParameters();
}