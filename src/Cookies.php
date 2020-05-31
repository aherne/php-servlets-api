<?php
namespace Lucinda\STDOUT;

/**
 * Encapsulates COOKIE operations and parameters
 */
class Cookies
{
    private $options;
    
    public function __construct(\Lucinda\STDOUT\Cookies\Options $options = null)
    {
        $this->options = $options;
    }
    
    /**
     * Adds/updates a cookie param.
     *
     * @param string $key
     * @param mixed $value
     * @param int $expirationTime
     */
    public function set(string $key, $value, int $expirationTime): void
    {
        if ($this->options) {
            setcookie($key, $value, time()+$expirationTime, $this->options->getPath(), $this->options->getDomain(), $this->options->isSecuredByHTTPS(), $this->options->isSecuredByHTTPheaders());
            $_COOKIE[$key] = $value;
        } else {
            setcookie($key, $value, time()+$expirationTime, "/");
            $_COOKIE[$key] = $value;
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
