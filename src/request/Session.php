<?php
require_once("SessionSecurityOptions.php");

/**
 * Attributes factory enveloping operations with SESSION. 
*/
final class Session {
	/**
	 * Starts session.
	 * 
	 * @param SessionSecurityOptions $objSessionSecurityOptions Added here to hint where to inject.
	 * @param SessionHandlerInterface $objSessionHandler	If null, built-in session handler is used.
	 */
	public function start(SessionSecurityOptions $objSessionSecurityOptions = null, SessionHandlerInterface $objSessionHandler = null) {
		if($objSessionHandler!=null) {
			session_set_save_handler($objSessionHandler, true);
		}
		session_start();
	}
	
	/**
	 * Checks if session is started.
	 * 
	 * @return boolean
	 */
	public function isStarted() {
		return (session_id() != "");
	}
	
	/**
	 * Closes session.
	 */
	public function destroy() {
		session_destroy();
	}
	
	/**
	 * Adds/updates a session param.
	 * 
	 * @param string $strKey
	 * @param mixed $mixValue
	 * @throws ServletException	If session not started.
	 */
	public function set($strKey, $mixValue) {
		if(!isset($_SESSION)) throw new ServletException("Session not started!");
		$_SESSION[$strKey] = $mixValue;
	}
	
	/**
	 * Gets session param value.
	 * 
	 * @param string $strKey
	 * @return mixed
	 * @throws ServletException	If session not started.
	 */
	public function get($strKey) {
		if(!isset($_SESSION)) throw new ServletException("Session not started!");
		return $_SESSION[$strKey];
	}
	
	/**
	 * Checks if session param exists.
	 * 
	 * @param string $strKey
	 * @return mixed
	 * @throws ServletException	If session not started.
	 */
	public function contains($strKey) {
		if(!isset($_SESSION)) throw new ServletException("Session not started!");
		return isset($_SESSION[$strKey]);
	}
	
	/**
	 * Deletes a session param.
	 * 
	 * @param string $strKey
	 * @throws ServletException	If session not started.
	 */
	public function remove($strKey) {
		if(!isset($_SESSION)) throw new ServletException("Session not started!");
		unset($_SESSION[$strKey]);
	}
}
