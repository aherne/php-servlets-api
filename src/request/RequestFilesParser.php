<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Encapsulates information from $_FILES superglobal into a tree.
 */
class RequestFilesParser {
	private $contents;

	/**
	 * Parses through $_FILES superglobal and compiles a tree.
	 */
	public function __construct() {
		$this->setResult();
	}

	/**
	 * Constructs tree
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
	 * Gets tree.
	 * 
	 * @return array
	 */
	public function getResult() {
		return $this->contents;
	}
}