<?php

namespace Lucinda\STDOUT;

use Lucinda\STDOUT\Session\Cookie;
use Lucinda\STDOUT\Session\Options;

/**
 * Encapsulates SESSION operations and parameters
 */
class Session
{
    /**
     * Configures session based on information set in XML "session" tag and starts it, if "auto_start" attribute is on
     *
     * @param Options|null $options
     */
    public function __construct(Options $options = null)
    {
        if ($options==null) {
            return;
        }

        $settings = $this->getSettings($options);
        foreach ($settings as $key=>$value) {
            ini_set("session.".$key, $value);
        }

        if ($className = $options->getHandler()) {
            session_set_save_handler(new $className(), true);
        }
    }

    /**
     * Gets settings to configure PHP session driver with
     *
     * @param  Options $options
     * @return array<string,int|string|bool>
     */
    private function getSettings(Options $options): array
    {
        $output = [];
        if ($value = $options->getSavePath()) {
            $output["save_path"] = $value;
        }
        if ($value = $options->getName()) {
            $output["name"] = $value;
        }
        if ($value = $options->getExpiredTime()) {
            $output["gc_maxlifetime"] = $value;
        }
        if ($value = $options->getExpiredOnBrowserClose()) {
            $output["cookie_lifetime"] = $value;
        }
        if ($value = $options->isSecuredByHTTPS()) {
            $output["cookie_secure"] = $value;
        }
        if ($value = $options->isSecuredByHTTPheaders()) {
            $output["cookie_httponly"] = $value;
        }
        if ($value = $options->getReferrerCheck()) {
            $output["referer_check"] = $value;
        }
        if ($options->isAutoStart()) {
            $output["auto_start"] = 1;
        }
        return $output;
    }

    /**
     * Starts session.
     */
    public function start(): bool
    {
        return session_start();
    }

    /**
     * Checks if session is started.
     *
     * @return boolean
     */
    public function isStarted(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Adds/updates a session param.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Gets session param value.
     *
     * @param  string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $_SESSION[$key];
    }

    /**
     * Checks if session param exists.
     *
     * @param  string $key
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
    public function destroy(): bool
    {
        $_SESSION = [];
        setcookie(session_name(), "", time()-60);
        return session_destroy();
    }

    /**
     * Terminates current session and discards all changes
     *
     * @return bool
     */
    public function abort(): bool
    {
        return session_abort();
    }

    /**
     * Terminates current session and saves all changes
     *
     * @return bool
     */
    public function commit(): bool
    {
        return session_write_close();
    }

    /**
     * Gets access to session cookie operations
     *
     * @return Cookie
     */
    public function cookie(): Cookie
    {
        return new Cookie();
    }
}
