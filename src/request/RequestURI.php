<?php
/**
 * Encapsulates information about URI client requested.
 */
final class RequestURI {
	private $strURL;
	private $strProtocol;
	private $strHost;
	private $strContextPath;
	private $strPage;
	private $strQueryString;
	private $tblParameters;
	
	public function __construct() {
		$this->setURL();
		$this->setProtocol();
		$this->setHost();
		$this->setContextPath();
		$this->setPage();
		$this->setQueryString();
		$this->setParameters();
	}
	
	/**
	 * Sets requested URL.
	 *
	 * @return void
	 */
	private function setURL() {
		$this->strURL = (isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	
	/**
	 * Gets requested URL.
	 *
	 * @example "http://www.test.com/servlets/test.html?a=b&c=d"
	 * @return string
	 */
	public function getURL() {
		return $this->strURL;
	}
	
	/**
	 * Sets protocol from requested URL.
	 */
	private function setProtocol() {
		$this->strProtocol = (isset($_SERVER['HTTPS'])?"https":"http");
	}
	
	/**
	 * Gets protocol from requested URL.
	 *
	 * @example "http" when url is "http://www.test.com/servlets/test.html?a=b&c=d"
	 * @return string
	 */
	public function getProtocol() {
		return $this->strProtocol;
	}
	
	/**
	 * Sets host name from requested URL.
	 */
	private function setHost() {
		$this->strHost = $_SERVER['HTTP_HOST'];
	}
	
	/**
	 * Gets host name from requested URL.
	 *
	 * @example "www.test.com" when url is "http://www.test.com/servlets/test.html?a=b&c=d"
	 * @return string
	 */
	public function getHost() {
		return $this->strHost;
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
	 * Extracts page from requested URL.
	 *
	 * @throws ServletException
	 */
	private function setPage() {
		// get page path and extension from request
	    $strURL = "";
	    $strExtension = "";
	    if(!isset($_SERVER["REQUEST_URI"])) throw new ServletException("ServletsAPI requires overriding paths!");
	    
	    // split it into page and extension
		$strURLCombined = $_SERVER["REQUEST_URI"];
		$intQuestionPosition = strpos($strURLCombined,"?");
		if($intQuestionPosition!==false) {
			$strURLCombined = substr($strURLCombined,0,$intQuestionPosition);
		}
		$this->strPage = ($strURLCombined?substr($strURLCombined,1):""); // remove trailing slash
	}
	
	/**
	 * Gets page requested path.
	 *
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
	 * Gets parameters originally sent by client in query string
	 */
	public function getParameters() {
		return $this->tblParameters;
	}
}
