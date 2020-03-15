<?php
namespace Lucinda\STDOUT\Cookies;

/**
 * Encapsulates cookie security settings on top of php.ini.
 */
class SecurityOptions
{
    private $expiredTime = 0;
    private $isHTTPSOnly = false;
    private $isHTTPHeadersOnly = false;
    private $path = "";
    private $domain = "";
        
    /**
     * Sets cookie's expiration time.
     *
     * @param integer $seconds
     */
    public function setExpiredTime(int $seconds): void
    {
        $this->expiredTime = time() + $seconds;
    }
    
    /**
     * Gets cookie's expiration time.
     *
     * @return integer
     */
    public function getExpiredTime(): int
    {
        return $this->expiredTime;
    }
    
    /**
     * Toggles restricting to HTTPS only. If ON: HTTP cookies will not be accepted by server.
     *
     * @param boolean $value
     */
    public function setSecuredByHTTPS(bool $value=false): void
    {
        $this->isHTTPS = $value;
    }
    
    /**
     * Gets whether or not cookie is available only through HTTPs.
     *
     * @return boolean
     */
    public function isSecuredByHTTPS(): bool
    {
        return $this->isHTTPS;
    }
    
    /**
     * Toggles restricting cookies to HTTP headers only. If ON: cookies not sent via HTTP headers will be ignored by server.
     * @param boolean $value
     */
    public function setSecuredByHTTPheaders(bool $value=false): void
    {
        $this->isHTTPHeadersOnly = $value;
    }
    
    /**
     * Gets whether or not cookie is available through HTTP only.
     *
     * @return boolean
     */
    public function isSecuredByHTTPheaders(): bool
    {
        return $this->isHTTPHeadersOnly;
    }
    
    /**
     * Sets the path on the server in which the cookie will be available on.
     *
     * @param string $path
     */
    public function setPath(string $path = ""): void
    {
        $this->path = $path;
    }
    
    /**
     * Gets the path on the server in which the cookie will be available on.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
    
    /**
     * Sets the (sub)domain that the cookie is available to.
     *
     * @param string $domain
     */
    public function setDomain(string $domain = ""): void
    {
        $this->domain = $domain;
    }

    /**
     * Gets the (sub)domain that the cookie is available to.
     *
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }
}
