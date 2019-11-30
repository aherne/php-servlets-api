<?php
namespace Lucinda\STDOUT;

use Lucinda\STDOUT\Session\SecurityOptions;

/**
 * Encapsulates SESSION operations and parameters
*/
class Session
{
    /**
     * Starts session.
     *
     * @param SecurityOptions $securityOptions Added here to hint where to inject.
     * @param \SessionHandlerInterface $sessionHandler	If null, built-in session handler is used.
     */
    public function start(SecurityOptions $securityOptions = null, \SessionHandlerInterface $sessionHandler = null)
    {
        if ($securityOptions!=null) {
            $securityOptions->save();
        }
        if ($sessionHandler!=null) {
            session_set_save_handler($sessionHandler, true);
        }
        session_start();
    }
    
    /**
     * Checks if session is started.
     *
     * @return boolean
     */
    public function isStarted()
    {
        return (session_id() != "");
    }
    
    /**
     * Closes session.
     */
    public function destroy()
    {
        session_destroy();
    }
    
    /**
     * Adds/updates a session param.
     *
     * @param string $key
     * @param mixed $value
     * @throws Exception	If session not started.
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Gets session param value.
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $_SESSION[$key];
    }
    
    /**
     * Checks if session param exists.
     *
     * @param string $key
     * @return mixed
     */
    public function contains($key)
    {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Deletes a session param.
     *
     * @param string $key
     */
    public function remove($key)
    {
        unset($_SESSION[$key]);
    }
}
