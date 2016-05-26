<?php
/**
 * Attributes factory enveloping operations with COOKIE.
 */
final class Cookie {
	/**
	 * Adds/updates a cookie param.
	 * 
	 * @param string $strKey
	 * @param mixed $mixValue
	 * @param integer $intExpiration Number of seconds until cookie expires.
	 * @param boolean $blnSecure Available only through HTTPS.
	 * @param boolean $blnHttpOnly Accessible only through HTTP protocol (cookie not accessible via JS).
	 */
	public function set($strKey, $mixValue, $intExpiration=0, $blnSecure=false, $blnHttpOnly=false) {
		$blnAnswer = setcookie($strKey, $mixValue, ($intExpiration!=0?time()+$intExpiration:0), "", "", $blnSecure, $blnHttpOnly);
		if(!$blnAnswer) throw new ServletException("Cookie could not be set!");
	}
	
	/**
	 * Gets value of cookie param.
	 * 
	 * @param string $strKey
	 * @return mixed
	 * @throws ServletException
	 */
	public function get($strKey) {
		if(!isset($_COOKIE[$strKey])) throw new ServletException("Cookie parameter not found!");
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