<?php
require_once("CookieSecurityOptions.php");

/**
 * Attributes factory enveloping operations with COOKIE.
 */
final class Cookie {
	/**
	 * Adds/updates a cookie param.
	 * 
	 * @param string $strKey
	 * @param mixed $mixValue
	 * @param CookieSecurityOptions $objSecurityOptions
	 */
	public function set($strKey, $mixValue, CookieSecurityOptions $objSecurityOptions=null) {
		$blnAnswer = false;
		if($objSecurityOptions) {
			$blnAnswer = setcookie($strKey, $mixValue, $objSecurityOptions->getExpiredTime(), $objSecurityOptions->getPath(), $objSecurityOptions->getDomain(), $objSecurityOptions->isSecuredByHTTPS(), $objSecurityOptions->isSecuredByHTTPheaders());
		} else {
			$blnAnswer = setcookie($strKey, $mixValue);
		}
		if(!$blnAnswer) throw new ServletException("Cookie could not be set!");
	}
	
	/**
	 * Gets value of cookie param.
	 * 
	 * @param string $strKey
	 * @return mixed
	 */
	public function get($strKey) {
		return $_COOKIE[$strKey];
	}
	
	/**
	 * Checks if cookie param exists.
	 * 
	 * @param string $strKey
	 * @return boolean
	 */
	public function contains($strKey) {
		return isset($_COOKIE[$strKey]);
	}
	
	/**
	 * Deletes cookie param.
	 * 
	 * @param string $strKey
	 */
	public function remove($strKey) {
		setcookie ($strKey, "", 1);
		setcookie ($strKey, false);
		unset($_COOKIE[$strKey]);
	}
}