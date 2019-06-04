<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Encapsulates information about URI client requested.
 */
class RequestURI {
	private $contextPath;
	private $page;
	private $queryString;
	private $parameters;
	
	/**
	 * Detects info based on values in $_SERVER superglobal
	 */
	public function __construct() {
		$this->setContextPath();
		$this->setPage();
		$this->setQueryString();
		$this->parameters = $_GET;
	}
	
	/**
	 * Sets context path from requested URL.
	 */
	private function setContextPath() {
		$this->contextPath = str_replace(array($_SERVER["DOCUMENT_ROOT"],"/index.php"),"",$_SERVER["SCRIPT_FILENAME"]);
	}
	
	/**
	 * Gets context path from requested URL.
	 *
	 * @example "/servlets/" when url is "http://www.test.com/servlets/test.html?a=b&c=d"
	 * @return string
	 */
	public function getContextPath() {
		return $this->contextPath;
	}
	
	/**
	 * Sets original page requested path based on REQUEST_URI
	 *
	 * @throws ServletException
	 */
	private function setPage() {
		if(!isset($_SERVER["REQUEST_URI"])) throw new ServletException("ServletsAPI requires overriding paths!");
	    
		// remove query string
		$urlCombined = substr($_SERVER["REQUEST_URI"],strlen($this->contextPath));
		$questionPosition = strpos($urlCombined,"?");
		if($questionPosition!==false) {
			$urlCombined = substr($urlCombined,0,$questionPosition);
		}
		$this->page = (strpos($urlCombined,"/")===0?substr($urlCombined,1):$urlCombined); // remove trailing slash
	}
	
	/**
	 * Gets original page requested path.
	 *
	 * @example "mypage.json" when url is "http://www.test.com/servlets/mypage.json?a=b&c=d"
	 * @return string
	 */
	public function getPage() {
		return $this->page;
	}
	
	/**
	 * Sets query string part from requested URL
	 */
	private function setQueryString() {
		$this->queryString = $_SERVER["QUERY_STRING"];
	}
	
	/**
	 * Gets query string part from requested URL.
	 *
	 * @example "a=b&c=d" when url is "http://www.test.com/servlets/mypage.json?a=b&c=d"
	 * @return string
	 */
	public function getQueryString() {
		return $this->queryString;
	}
	
	/**
	 * Gets query string parameters detected by optional name
	 *
	 * @param string $name
	 * @return mixed[string]|NULL|mixed
	 */
	public function parameters($name="") {
	    if(!$name) return $this->parameters;
	    else return (isset($this->parameters[$name])?$this->parameters[$name]:null);
	}
}
