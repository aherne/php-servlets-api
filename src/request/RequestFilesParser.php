<?php
namespace Lucinda\MVC\STDOUT;

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
	private $contents;

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
						$this->contents[$k1][$k2] = $this->parseRecursive($name, $v2, (isset($this->contents[$k1][$k2])?$this->contents[$k1][$k2]:array()));
					}
				} else {
					$this->contents[$k1][$name] = $value;
				}
			}
		}
	}

	/**
	 * Recursively setResult() helper algorithm that merges information recursively.
	 * 
	 * @param string $name
	 * @param array $v
	 * @param array $oldArray
	 */
	private function parseRecursive($name, $part, $oldArray=array()) {
		$output = $oldArray;
		foreach($part as $key=>$value) {
			if(is_array($value)) {
				$output[$key] = $this->parseRecursive($name, $value, (!empty($oldArray)?$oldArray[$key]:array()));
			} else {
				$output[$key][$name] = $value;
			}
		}
		return $output;
	}

	/**
	 * Gets beautified array.
	 * 
	 * @return array
	 */
	public function getResult() {
		return $this->contents;
	}
}