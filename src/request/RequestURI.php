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
	private $strExtension;
	private $strQueryString;
	private $tblParameters;
	
	public function __construct() {
		$this->setURL();
		$this->setProtocol();
		$this->setHost();
		$this->setContextPath();
		$this->setPageInfo();
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
	 * Extracts page name and extension parts from requested URL.
	 *
	 * @throws ServletException
	 */
	private function setPageInfo() {
		// get page path and extension from request
	    	$blnFound = false;
	    	$strURL = "";
	    	$strExtension = "";
	    	$tblPossibleHolders = array("SCRIPT_URI","SCRIPT_URL","REDIRECT_URL","REQUEST_URI");
		foreach($tblPossibleHolders as $strVariable) {
			if(isset($_SERVER[$strVariable]) && strpos($_SERVER[$strVariable],"http")!==0) {
				$strURLCombined = $_SERVER[$strVariable];
		        	$intQuestionPosition = strpos($strURLCombined,"?");
		           	if($intQuestionPosition!==false) {
		               		$strURLCombined = substr($strURLCombined,0,$intQuestionPosition);
		           	}
			       	$intDotPosition = strrpos($strURLCombined,".");
	        	   	if($intDotPosition!==false) {
	        	       		$strURL = substr($strURLCombined,0, $intDotPosition);
	        	       		$strExtension = strtolower(substr($strURLCombined,($intDotPosition+1)));
	        	       		$intSlashPosition = strpos($strExtension, "/"); // this is when both path parameters and extension are supplied
	        	       		if($intSlashPosition!==false) {
	        	           		$strURL .= substr($strExtension, $intSlashPosition);
	        	           		$strExtension = substr($strExtension,0,$intSlashPosition);
	        	       		}
	        	   	} else {
	        	       		$strURL = $strURLCombined;
	        	   	}
	        	   	$strURL = substr($strURL, (strpos($strURL, $this->strContextPath)+strlen($this->strContextPath)));
	        	   	$blnFound = true;
		           	break;
		       	}
		}
		if(!$blnFound) {
			throw new ServletException("ServletsAPI requires overriding paths!");
		}
		
		// write url page
		$this->setPagePath($strURL);
	
		// write url extension
		$this->setPageExtension($strExtension);
	}
	
	/**
	 * Sets page path from requested URL.
	 *
	 * @return string
	 */
	private function setPagePath($strURL) {
		if($strURL!="/") {
			$this->strPage = (strpos($strURL,"/")===0?substr($strURL,1):$strURL);
		} else {
			$this->strPage = "";
		}
	}
	
	/**
	 * Gets page path from requested URL.
	 *
	 * @example "mypage" when url is "http://www.test.com/servlets/mypage.html?a=b&c=d"
	 * @return string
	 */
	public function getPagePath() {
		return $this->strPage;
	}
	
	/**
	 * Sets page extension part of requested URL.
	 * - If extension is not supplied,  it is replaced with default extension defined in XML as application.default_extension.
	 *
	 * @param string $strExtension
	 */
	private function setPageExtension($strExtension) {
		$this->strExtension = $strExtension;
	}
	
	/**
	 * Gets page extension from requested URL.
	 *
	 * @example "json" when url is "http://www.test.com/servlets/mypage.json?a=b&c=d"
	 * @return string
	 */
	public function getPageExtension() {
		return $this->strExtension;
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
	public function setParameters() {
		$this->tblParameters = $_GET;
	}
	
	/**
	 * Gets parameters originally sent by client in query string
	 */
	public function getParameters() {
		return $this->tblParameters;
	}
}
