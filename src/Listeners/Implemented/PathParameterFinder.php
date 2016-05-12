<?php
/**
 * Matches a pattern path with a requested path and collects:
 * - path parameters
 * - path without parameters
 * Example:
 * - pattern path: a/{b}/{c}
 * - requested path: a/1/2
 * =>
 * - path parameters: array("b"=>1,"c"=>2)
 * - path without parameters: a
 */
class PathParameterFinder {
	private $blnSuccess = false;
	private $strPath = "";
	private $tblParameters = array();
	
	public function __construct($strPatternPath, $strRequestedPath) {
		$this->blnSuccess = $this->match($strPatternPath, $strRequestedPath);
	}
	
	/**
	 * Performs the matching algorithm.
	 * 
	 * @param string $strPatternPath
	 * @param string $strRequestedPath
	 * @return boolean
	 */
	private function match($strPatternPath, $strRequestedPath) {
		$p1 = explode("/",$strPatternPath);
		$p2 = explode("/",$strRequestedPath);
		$strAlternatePath = "";
		$tblParameters = array();
		if(sizeof($p1) != sizeof($p2)) return false; 
		foreach($p1 as $k1=>$v1) {
			if($v1==$p2[$k1]) {
				$strAlternatePath.=$v1."/";
			} else if(strpos($v1,"{")!==false) {
				$tblParameters[str_replace(array("{","}"),"",$v1)]=$p2[$k1];
			} else {
				return false;
			}
		}
		$this->strPath = substr($strAlternatePath,0,-1);
		$this->tblParameters = $tblParameters;
		return true;
	}
	
	/**
	 * Verifies if a match was successful.
	 * 
	 * @return boolean
	 */
	public function isFound() {
		return $this->blnSuccess;
	}
	
	/**
	 * Gets path without parameters.
	 * 
	 * @return string
	 */
	public function getPath() {
		return $this->strPath;
	}
	
	/**
	 * Gets path parameters.
	 * 
	 * @return array
	 */
	public function getParameters() {
		return $this->tblParameters;
	}
}