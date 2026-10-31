<?php

namespace Lucinda\STDOUT;

use Lucinda\MVC\Facet;
use Lucinda\STDOUT\XmlTags\CookiesOptions;

/**
 * Provides mutable access to HTTP cookies as an injectable MVC facet
 *
 * Writes are sent through setcookie() and mirrored into $_COOKIE so later code
 * in the same request observes the new value. Optional XML settings supply the
 * default path, domain, secure, and HTTP-only flags for created cookies.
 */
final class Cookies implements Facet
{
    /**
     * Configured cookie defaults, or null to use path `/` and PHP defaults
     */
    private ?CookiesOptions $options = null;

    /**
     * Creates a cookie facade with optional XML defaults
     *
     * @param CookiesOptions|null $options Defaults used for subsequent cookie writes
     */
    public function __construct(?CookiesOptions $options = null)
    {
        $this->options = $options;
    }

    /**
     * Creates or replaces a cookie
     *
     * @param string $key Cookie name
     * @param mixed $value Value passed to setcookie() and stored in $_COOKIE
     * @param int $expirationTime Lifetime in seconds from the current time
     */
    public function set(string $key, mixed $value, int $expirationTime): void
    {
        setcookie($key, $value, $this->getCookieParameters(time()+$expirationTime));
        $_COOKIE[$key] = $value;
    }

    /**
     * Gets a cookie value
     *
     * @param string $key Cookie name expected to exist
     * @return mixed Current value from $_COOKIE
     */
    public function get(string $key): mixed
    {
        return $_COOKIE[$key];
    }

    /**
     * Checks whether a cookie has a non-null value
     *
     * @param string $key Cookie name
     * @return bool True when the cookie exists in $_COOKIE
     */
    public function contains(string $key): bool
    {
        return isset($_COOKIE[$key]);
    }

    /**
     * Expires a cookie and removes it from the current request state
     *
     * @param string $key Cookie name
     */
    public function remove(string $key): void
    {
        setcookie($key, "", $this->getCookieParameters(time()-3600));
        unset($_COOKIE[$key]);
    }
    /**
     * Shares cookie scope and attributes between creation and deletion.
     *
     * @return array<string,int|string|bool>
     */
    private function getCookieParameters(int $expires): array
    {
        $parameters = [
            "expires" => $expires,
            "path" => $this->options?->getPath() ?? "/",
            "domain" => $this->options?->getDomain() ?? "",
            "secure" => $this->options?->isSecuredByHTTPS() ?? false,
            "httponly" => $this->options?->isSecuredByHTTPheaders() ?? false
        ];
        if (($sameSite = $this->options?->getSameSite()) !== null) {
            $parameters["samesite"] = $sameSite;
        }
        return $parameters;
    }

}
