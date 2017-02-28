<?php
require_once("UploadedFile.php");
require_once("RequestFilesParser.php");
 
/**
 * Encapsulates information of files sent via multipart forms into UploadedFile objects.
 */
final class UploadedFileTree {
	protected $tblAttributes =  array();
		
	public function __construct() {
		if(sizeof($_FILES)==0) return;
		$objRequestParser = new RequestFilesParser();
		$this->tblAttributes = $this->makeObjects($objRequestParser->getResult());
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
				$uploadedFile = new UploadedFile($array);
				if($uploadedFile->getName()===null) {
					return null;
				} else {
					return $uploadedFile;
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
		return $this->tblAttributes;
	}
}
