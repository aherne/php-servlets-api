<?php
require_once("UploadedFile.php");
require_once("RequestFilesParser.php");
 
/**
 * Encapsulates information of files sent via multipart forms into UploadedFile objects.
 */
final class UploadedFileTree {
	protected $attributes =  array();
		
	public function __construct() {
		if(sizeof($_FILES)==0) return;
		$requestParser = new RequestFilesParser();
		$this->attributes = $this->makeObjects($requestParser->getResult());
	}
	
	/**
	 * Performs recursive conversion between array of properties and UploadedFile
	 * 
	 * @param array $array
	 */
	private function makeObjects($array) {
		$ret = array();
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$ret[$k] = $this->makeObjects($v);
			} else {
				if($array["error"]!=UPLOAD_ERR_NO_FILE) {
					return new UploadedFile($array);
				} else {
					return null; // having no file uploaded is a non-error situation
				}
			}
		}
		return $ret;
	}
	
	/**
	 * Decapsulates attributes as array.
	 * 
	 * @return array
	 */
	public function toArray() {
		return $this->attributes;
	}
}
