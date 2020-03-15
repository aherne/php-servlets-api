<?php
namespace Lucinda\STDOUT;

use Lucinda\STDOUT\Cookies\SecurityOptions;

/**
 * Encapsulates COOKIE operations and parameters
 */
class Cookies
{
    const DEFAULT_EXPIRATION_TIME = 3600;
    
    /**
     * Adds/updates a cookie param.
     *
     * @param string $key
     * @param mixed $value
     * @param SecurityOptions $securityOptions
     * @throws Exception
     */
    public function set(string $key, $value, SecurityOptions $securityOptions=null): void
    {
        $answer = false;
        if ($securityOptions) {
            $answer = setcookie($key, $value, $securityOptions->getExpiredTime(), $securityOptions->getPath(), $securityOptions->getDomain(), $securityOptions->isSecuredByHTTPS(), $securityOptions->isSecuredByHTTPheaders());
            $_COOKIE[$key] = $value;
        } else {
            $answer = setcookie($key, $value, time()+self::DEFAULT_EXPIRATION_TIME);
            $_COOKIE[$key] = $value;
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
    public function get(string $key)
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
