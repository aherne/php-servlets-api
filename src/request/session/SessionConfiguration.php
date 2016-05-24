<?php
/**
 * Encapsulates configuration of sessions above PHP.ini settings.
 */
final class SessionConfiguration {
	/**
	 * Set path that is going to be used when storing sessions.
	 * 
	 * @param string $strPath
	 * @throws ServletException
	 */
	public function setSavePath($strPath) {
		if(!file_exists($path)) throw new ServletException("Session path not found!");
		ini_set("session.save_path",$strPath);
	}
	
	/**
	 * Sets name of session cookie.
	 * 
	 * @param string $strName
	 */
	public function setName($strName) {
		ini_set("session.name",$strName);
	}
	
	/**
	 * Sets a built in session handler (compiled along with PHP).
	 * 
	 * @param string $strHandlerName
	 */
	public function setHandler($strHandlerName="file") {
		ini_set("session.save_handler", $strKeyword);
	}
	
	/**
	 * Sets session cookie's expiration time.
	 * 
	 * @param integer $intSeconds
	 */
	public function setExpiredTime($intSeconds) {
		ini_set("session.gc_maxlifetime",$intSeconds);
	}
	
	/**
	 * Toggles session expiration on browser close.
	 * 
	 * @param boolean $blnValue
	 */
	public function setExpiredOnBrowserClose($blnValue=false) {
		ini_set("session.cookie_lifetime", ($blnValue?1:0));
	}
	
	/**
	 * Toggles restricting sessions to HTTPS only. If ON: HTTP cookies will not be accepted by server.
	 * 
	 * @param boolean $blnValue
	 */
	public function setSecuredByHTTPS($blnValue=false) {
		ini_set("session.cookie_secure", ($blnValue?1:0));
	}
	
	/**
	 * Toggles restricting sessions to HTTP headers only. If ON: cookies not sent via HTTP headers will be ignored by server.
	 * @param boolean $blnValue
	 */
	public function setSecuredByHTTPheaders($blnValue=false) {
		ini_set("session.cookie_httponly", ($blnValue?1:0));
	}
		
	/**
	 * Toggles restricting sessions to those coming with a HTTP referrer LIKE %keyword%.
	 * 
	 * @param string $strKeyword
	 */
	public function setSecuredByReferrerCheck($strKeyword) {
		ini_set("session.referer_check", $strKeyword);
	}
}