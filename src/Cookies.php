<?php
namespace Lucinda\STDOUT;

use Lucinda\STDOUT\Cookies\SecurityOptions;

/**
 * Encapsulates COOKIE operations and parameters
 */
class Cookies
{
    /**
     * Adds/updates a cookie param.
     *
     * @param string $key
     * @param mixed $value
     * @param SecurityOptions $securityOptions
     * @throws Exception
     */
    public function set(string $key, mixed $value, SecurityOptions $securityOptions=null): void
    {
        $answer = false;
        if ($securityOptions) {
            $answer = setcookie($key, $value, $securityOptions->getExpiredTime(), $securityOptions->getPath(), $securityOptions->getDomain(), $securityOptions->isSecuredByHTTPS(), $securityOptions->isSecuredByHTTPheaders());
        } else {
            $answer = setcookie($key, $value);
        }
        if (!$answer) {
            throw new Exception("Cookie could not be set!");
        }
    }
    
    /**
     * Gets value of cookie param.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $_COOKIE[$key];
    }
    
    /**
     * Checks if cookie param exists.
     *
     * @param string $key
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
        setcookie($key, "", 1);
        setcookie($key, false);
        unset($_COOKIE[$key]);
    }
}
