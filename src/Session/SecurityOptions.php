<?php
namespace Lucinda\STDOUT\Session;

/**
 * Encapsulates session security settings on top of php.ini.
 */
class SecurityOptions
{
    private $settings = [];
    
    /**
     * Set path that is going to be used when storing sessions.
     *
     * @param string $path
     */
    public function setSavePath(string $path): void
    {
        $this->settings["session.save_path"] = $path;
    }
    
    /**
     * Sets name of session cookie.
     *
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->settings["session.name"] = $name;
    }
    
    /**
     * Sets session cookie's expiration time.
     *
     * @param integer $seconds
     */
    public function setExpiredTime(int $seconds): void
    {
        $this->settings["session.gc_maxlifetime"] = $seconds;
    }
    
    /**
     * Toggles session expiration on browser close.
     *
     * @param boolean $value
     */
    public function setExpiredOnBrowserClose(bool $value=false): void
    {
        $this->settings["session.cookie_lifetime"] = ($value?1:0);
    }
    
    /**
     * Toggles restricting sessions to HTTPS only. If ON: HTTP cookies will not be accepted by server.
     *
     * @param boolean $value
     */
    public function setSecuredByHTTPS(bool $value=false): void
    {
        $this->settings["session.cookie_secure"] = ($value?1:0);
    }
    
    /**
     * Toggles restricting sessions to HTTP headers only. If ON: cookies not sent via HTTP headers will be ignored by server.
     * @param boolean $value
     */
    public function setSecuredByHTTPheaders(bool $value=false): void
    {
        $this->settings["session.cookie_httponly"] = ($value?1:0);
    }
        
    /**
     * Toggles restricting sessions to those coming with a HTTP referrer LIKE %keyword%.
     *
     * @param string $keyword
     */
    public function setSecuredByReferrerCheck(string $keyword): void
    {
        $this->settings["session.referer_check"] = $keyword;
    }
    
    /**
     * Saves settings to php ini
     */
    public function save(): void
    {
        foreach ($this->settings as $key=>$value) {
            ini_set($key, $value);
        }
    }
}
