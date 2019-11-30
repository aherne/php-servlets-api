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
    public function set($key, $value, SecurityOptions $securityOptions=null)
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
    public function get($key)
    {
        return $_COOKIE[$key];
    }
    
    /**
     * Checks if cookie param exists.
     *
     * @param string $key
     * @return boolean
     */
    public function contains($key)
    {
        return isset($_COOKIE[$key]);
    }
    
    /**
     * Deletes cookie param.
     *
     * @param string $key
     */
    public function remove($key)
    {
        setcookie($key, "", 1);
        setcookie($key, false);
        unset($_COOKIE[$key]);
    }
}
