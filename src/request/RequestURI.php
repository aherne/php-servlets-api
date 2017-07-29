<?php
/**
 * Encapsulates information about URI client requested.
 */
final class RequestURI {
	private $strContextPath;
	private $strPage;
	private $strQueryString;
	private $tblParameters;
	
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
		$this->strContextPath = str_replace(array($_SERVER["DOCUMENT_ROOT"],"/index.php"),"",$_SERVER["SCRIPT_FILENAME"]);
	}
	
	/**
	 * Gets context path from requested URL.
	 *
	 * @example "/servlets/" when url is "http://www.test.com/servlets/test.html?a=b&c=d"
	 * @return string
	 */
	public function getContextPath() {
		return $this->strContextPath;
	}
	
	/**
	 * Sets original page requested path based on REQUEST_URI
	 *
	 * @throws ServletException
	 */
	private function setPage() {
		if(!isset($_SERVER["REQUEST_URI"])) throw new ServletException("ServletsAPI requires overriding paths!");
	    
		// remove query string
		$strURLCombined = substr($_SERVER["REQUEST_URI"],strlen($this->strContextPath));
		$intQuestionPosition = strpos($strURLCombined,"?");
		if($intQuestionPosition!==false) {
			$strURLCombined = substr($strURLCombined,0,$intQuestionPosition);
		}
		$this->strPage = (strpos($strURLCombined,"/")===0?substr($strURLCombined,1):$strURLCombined); // remove trailing slash
	}
	
	/**
	 * Gets original page requested path.
	 *
	 * @example "mypage.json" when url is "http://www.test.com/servlets/mypage.json?a=b&c=d"
	 * @return string
	 */
	public function getPage() {
		return $this->strPage;
	}
	
	/**
	 * Sets query string part from requested URL
	 */
	private function setQueryString() {
		$this->strQueryString = $_SERVER["QUERY_STRING"];
	}
	
	/**
	 * Gets query string part from requested URL.
	 *
	 * @example "a=b&c=d" when url is "http://www.test.com/servlets/mypage.json?a=b&c=d"
	 * @return string
	 */
	public function getQueryString() {
		return $this->strQueryString;
	}
	
	/**
	 * Sets parameters sent by client from PHP superglobal $_GET.
	 */
	private function setParameters() {
		$this->tblParameters = $_GET;
	}
	
	/**
	 * Gets value of GET parameter
	 *
	 * @param string $name
	 * @return mixed|null Null if parameter doesn't exist, mixed otherwise.
	 */
	public function getParameter($name) {
		return (isset($this->tblParameters[$name])?$this->tblParameters[$name]:null);
	}
	
	/**
	 * Gets all GET parameters received
	 *
	 * @return array
	 */
	public function getParameters() {
		return $this->tblParameters;
	}
}
