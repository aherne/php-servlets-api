<?php
namespace Lucinda\STDOUT;

use Lucinda\MVC\Locators\ClassFinder;

/**
 * Encapsulates SESSION operations and parameters
*/
class Session
{
    /**
     * Configures session based on information set in XML "session" tag and starts it, if "auto_start" attribute is on
     *
     * @param \Lucinda\STDOUT\Session\Options $options
     */
    public function __construct(\Lucinda\STDOUT\Session\Options $options = null)
    {
        if ($options==null) {
            return;
        }
        
        if ($value = $options->getSavePath()) {
            ini_set("session.save_path", $value);
        }
        if ($value = $options->getName()) {
            ini_set("session.name", $value);
        }
        if ($value = $options->getExpiredTime()) {
            ini_set("session.gc_maxlifetime", $value);
        }
        if ($value = $options->getExpiredOnBrowserClose()) {
            ini_set("session.cookie_lifetime", $value);
        }
        if ($value = $options->isSecuredByHTTPS()) {
            ini_set("session.cookie_secure", $value);
        }
        if ($value = $options->isSecuredByHTTPheaders()) {
            ini_set("session.cookie_httponly", $value);
        }
        if ($value = $options->getReferrerCheck()) {
            ini_set("session.referer_check", $value);
        }
        if ($value = $options->getHandler()) {
            $classFinder = new ClassFinder("");
            $className = $classFinder->find($value);
            session_set_save_handler(new $className(), true);
        }
        if ($value = $options->isAutoStart()) {
            $this->start();
        }
    }
    
    /**
     * Starts session.
     */
    public function start(): void
    {
        session_start();
    }
    
    /**
     * Checks if session is started.
     *
     * @return boolean
     */
    public function isStarted(): bool
    {
        return (session_id() != "");
    }
    
    /**
     * Adds/updates a session param.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Gets session param value.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $_SESSION[$key];
    }
    
    /**
     * Checks if session param exists.
     *
     * @param string $key
     * @return bool
     */
    public function contains(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Deletes a session param.
     *
     * @param string $key
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }
    
    /**
     * Closes session.
     */
    public function destroy(): void
    {
        session_destroy();
    }
}
