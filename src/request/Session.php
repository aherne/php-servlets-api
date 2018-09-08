<?php
namespace Lucinda\MVC\STDOUT;

require_once("SessionSecurityOptions.php");

/**
 * Attributes factory enveloping operations with SESSION. 
*/
final class Session {
	/**
	 * Starts session.
	 * 
	 * @param SessionSecurityOptions $sessionSecurityOptions Added here to hint where to inject.
	 * @param \SessionHandlerInterface $sessionHandler	If null, built-in session handler is used.
	 */
	public function start(SessionSecurityOptions $sessionSecurityOptions = null, \SessionHandlerInterface $sessionHandler = null) {
		if($sessionHandler!=null) {
			session_set_save_handler($sessionHandler, true);
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
	 * @param string $key
	 * @param mixed $value
	 * @throws ServletException	If session not started.
	 */
	public function set($key, $value) {
		if(!isset($_SESSION)) throw new ServletException("Session not started!");
		$_SESSION[$key] = $value;
	}
	
	/**
	 * Gets session param value.
	 * 
	 * @param string $key
	 * @return mixed
	 * @throws ServletException	If session not started.
	 */
	public function get($key) {
		if(!isset($_SESSION)) throw new ServletException("Session not started!");
		return $_SESSION[$key];
	}
	
	/**
	 * Checks if session param exists.
	 * 
	 * @param string $key
	 * @return mixed
	 * @throws ServletException	If session not started.
	 */
	public function contains($key) {
		if(!isset($_SESSION)) throw new ServletException("Session not started!");
		return isset($_SESSION[$key]);
	}
	
	/**
	 * Deletes a session param.
	 * 
	 * @param string $key
	 * @throws ServletException	If session not started.
	 */
	public function remove($key) {
		if(!isset($_SESSION)) throw new ServletException("Session not started!");
		unset($_SESSION[$key]);
	}
}
