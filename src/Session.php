<?php

namespace Lucinda\STDOUT;

use Lucinda\MVC\Facet;
use Lucinda\STDOUT\XmlTags\SessionOptions;
use Lucinda\STDOUT\Session\Cookie;

/**
 * Configures and exposes PHP session state as an injectable MVC facet
 *
 * Optional XML settings are translated into session.* ini directives during
 * construction. A configured handler class is instantiated without arguments
 * and registered as PHP's session save handler. Session lifecycle operations
 * remain explicit unless the configuration enables PHP auto-start.
 */
final class Session implements Facet
{
    /**
     * Applies optional session configuration and registers its save handler
     *
     * @param SessionOptions|null $options Parsed settings, or null to leave PHP defaults unchanged
     * @throws \Throwable If a configured handler cannot be constructed or registered
     */
    public function __construct(?SessionOptions $options = null)
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
     * Converts parsed options into session ini keys and values
     *
     * Cookie security flags preserve explicit false values. Other settings use
     * explicitly configured truthy values, except auto_start,
     * which is emitted as integer 1 when enabled.
     *
     * @param SessionOptions $options Parsed session configuration
     * @return array<string,int|string|bool> Values keyed without the `session.` prefix
     */
    private function getSettings(SessionOptions $options): array
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
        if (($value = $options->getExpiredOnBrowserClose()) !== null) {
            $output["cookie_lifetime"] = $value;
        }
        if (($value = $options->isSecuredByHTTPS()) !== null) {
            $output["cookie_secure"] = (int) $value;
        }
        if (($value = $options->isSecuredByHTTPheaders()) !== null) {
            $output["cookie_httponly"] = (int) $value;
        }
        if (($value = $options->getSameSite()) !== null) {
            $output["cookie_samesite"] = $value;
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
     * Starts or resumes the PHP session
     *
     * @return bool True when PHP starts the session successfully
     */
    public function start(): bool
    {
        return session_start();
    }

    /**
     * Checks whether a PHP session is active
     *
     * @return bool True when session_status() is PHP_SESSION_ACTIVE
     */
    public function isStarted(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Creates or replaces a session value
     *
     * @param string $key Session key
     * @param mixed $value Value stored in $_SESSION
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Gets a session value
     *
     * @param string $key Session key expected to exist
     * @return mixed Current value from $_SESSION
     */
    public function get(string $key): mixed
    {
        return $_SESSION[$key];
    }

    /**
     * Checks whether a session key has a non-null value
     *
     * @param string $key Session key
     * @return bool True when the key exists in $_SESSION
     */
    public function contains(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Removes a value from the current session
     *
     * @param string $key Session key
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Clears session data, expires the ID cookie, and destroys server state
     *
     * @return bool True when PHP destroys the active session successfully
     */
    public function destroy(): bool
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $parameters = session_get_cookie_params();
            unset($parameters["lifetime"]);
            $parameters["expires"] = time()-60;
            setcookie(session_name(), "", $parameters);
            unset($_COOKIE[session_name()]);
        }
        return session_destroy();
    }

    /**
     * Closes the current session and discards changes made in this request
     *
     * @return bool True when PHP aborts the active session successfully
     */
    public function abort(): bool
    {
        return session_abort();
    }

    /**
     * Persists changes and closes the current session
     *
     * @return bool True when PHP writes and closes the active session successfully
     */
    public function commit(): bool
    {
        return session_write_close();
    }

    /**
     * Gets a facade for the current session ID cookie
     *
     * @return Cookie Session identifier operations backed by PHP's session API
     */
    public function cookie(): Cookie
    {
        return new Cookie();
    }
}
