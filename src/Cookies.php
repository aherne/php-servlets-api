<?php

namespace Lucinda\STDOUT;

use Lucinda\MVC\Facet;
use Lucinda\STDOUT\Cookies\Options;
use Lucinda\STDOUT\Facets\CookiesOptions;

/**
 * Encapsulates COOKIE operations and parameters
 */
final class Cookies implements Facet
{
    private ?CookiesOptions $options = null;

    /**
     * Sets up cookies based on options.
     *
     * @param CookiesOptions|null $options
     */
    public function __construct(?CookiesOptions $options = null)
    {
        $this->options = $options;
    }

    /**
     * Adds/updates a cookie param.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $expirationTime
     */
    public function set(string $key, mixed $value, int $expirationTime): void
    {
        if ($this->options) {
            setcookie(
                $key,
                $value,
                time()+$expirationTime,
                $this->options->getPath(),
                $this->options->getDomain(),
                $this->options->isSecuredByHTTPS(),
                $this->options->isSecuredByHTTPheaders()
            );
            $_COOKIE[$key] = $value;
        } else {
            setcookie($key, $value, time()+$expirationTime, "/");
            $_COOKIE[$key] = $value;
        }
    }

    /**
     * Gets value of cookie param.
     *
     * @param  string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $_COOKIE[$key];
    }

    /**
     * Checks if cookie param exists.
     *
     * @param  string $key
     * @return boolean
     */
    public function contains(string $key): bool
    {
        return isset($_COOKIE[$key]);
    }

    /**
     * Deletes cookie param.
     *
     * @param string $key
     */
    public function remove(string $key): void
    {
        setcookie($key, "", time()-3600);
        unset($_COOKIE[$key]);
    }
}
