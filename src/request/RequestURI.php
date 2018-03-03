<?php
/**
 * Encapsulates information about URI client requested.
 */
final class RequestURI {
	private $contextPath;
	private $page;
	private $queryString;
	private $parameters;
	
	public function __construct() {
		$this->setContextPath();
		$this->setPage();
		$this->setQueryString();
		$this->setParameters();
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
		$uRLCombined = substr($_SERVER["REQUEST_URI"],strlen($this->contextPath));
		$questionPosition = strpos($uRLCombined,"?");
		if($questionPosition!==false) {
			$uRLCombined = substr($uRLCombined,0,$questionPosition);
		}
		$this->page = (strpos($uRLCombined,"/")===0?substr($uRLCombined,1):$uRLCombined); // remove trailing slash
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
	 * Sets parameters sent by client from PHP superglobal $_GET.
	 */
	private function setParameters() {
		$this->parameters = $_GET;
	}
	
	/**
	 * Gets value of GET parameter
	 *
	 * @param string $name
	 * @return mixed|null Null if parameter doesn't exist, mixed otherwise.
	 */
	public function getParameter($name) {
		return (isset($this->parameters[$name])?$this->parameters[$name]:null);
	}
	
	/**
	 * Gets all GET parameters received
	 *
	 * @return array
	 */
	public function getParameters() {
		return $this->parameters;
	}
}
