<?php
/**
 * Parses information of files sent via multipart forms into a beautified array.
 * 
 * Example:
 * 		array (
 * 			"a"=>array(
 * 				"name"=>array(
 * 					1=>"a.jpg",
 * 					2=>"b.jpg"
 * 				),
 * 				...
 * 			)
 * 		)
 * 		becomes:
 * 		array (
 * 			"a"=>array(
 * 				1=>array(
 * 					"name"=>"a.jpg",
 * 					...
 * 				),
 * 2=>array("name"=>"b.jpg",...)));
 */
class RequestFilesParser {
	private $tblContents;

	public function __construct() {
		$this->setResult();
	}

	/**
	 * Constructs beautified array
	 */
	private function setResult() {
		foreach($_FILES as $k1=>$v1) {
			foreach($v1 as $name=>$value) {
				if(is_array($value)) {
					foreach($value as $k2=>$v2) {
						$this->tblContents[$k1][$k2] = $this->parseRecursive($name, $v2, (isset($this->tblContents[$k1][$k2])?$this->tblContents[$k1][$k2]:array()));
					}
				} else {
					$this->tblContents[$k1][$name] = $value;
				}
			}
		}
	}

	/**
	 * Recursively setResult() helper algorithm that merges information recursively.
	 * 
	 * @param string $strName
	 * @param array $v
	 * @param array $oldArray
	 */
	private function parseRecursive($strName, $v, $oldArray=array()) {
		$tblOutput = $oldArray;
		foreach($v as $key=>$value) {
			if(is_array($value)) {
				$tblOutput[$key] = $this->parseRecursive($strName, $value, (!empty($oldArray)?$oldArray[$key]:array()));
			} else {
				$tblOutput[$key][$strName] = $value;
			}
		}
		return $tblOutput;
	}

	/**
	 * Gets beautified array.
	 * 
	 * @return array
	 */
	public function getResult() {
		return $this->tblContents;
	}
}