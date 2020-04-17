<?php
namespace Lucinda\STDOUT\Session;

/**
 * Sets options to create session with based on contents of XML tag "session"
 */
class Options
{
    private $savePath = "";
    private $name = "";
    private $expiredTime = 0;
    private $expiredOnBrowserClose = 0;
    private $isSecuredByHTTPS = false;
    private $isSecuredByHTTPheaders = false;
    private $referrerCheck = "";
    private $handlerFile = "";
    private $autoStart = false;
    
    /**
     * Saves session options based on XML tag "session"
     *
     * @param \SimpleXMLElement $info
     */
    public function __construct(\SimpleXMLElement $info)
    {
        $this->savePath = (string) $info["save_path"];
        $this->name = (string) $info["name"];
        $this->expiredTime = (int) $info["expired_time"];
        $this->expiredOnBrowserClose = (int) $info["expired_on_close"];
        $this->isSecuredByHTTPS = (bool) $info["https_only"];
        $this->isSecuredByHTTPheaders = (bool) $info["headers_only"];
        $this->referrerCheck = (string) $info["referrer_check"];
        $this->handlerFile = (string) $info["handler"];
        $this->autoStart = (bool) $info["auto_start"];
    }
    
    /**
     * Gets path that is going to be used when storing sessions.
     *
     * @return string
     */
    public function getSavePath(): string
    {
        return $this->savePath;
    }
        
    /**
     * Gets name of session cookie.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Gets session cookie's expiration time.
     *
     * @return int
     */
    public function getExpiredTime(): int
    {
        return $this->expiredTime;
    }
    
    /**
     * Gets session expiration time on browser close.
     *
     * @return int
     */
    public function getExpiredOnBrowserClose(): int
    {
        return $this->expiredOnBrowserClose;
    }
    
    /**
     * Gets whether or not sessions are accepted only if protocol is HTTPS
     *
     * @return bool
     */
    public function isSecuredByHTTPS(): bool
    {
        return $this->isSecuredByHTTPS;
    }
        
    /**
     * Gets whether or not session id cookie is available to client via JavaScript
     *
     * @return bool
     */
    public function isSecuredByHTTPheaders(): bool
    {
        return $this->isSecuredByHTTPheaders;
    }
        
    /**
     * Gets HTTP referrer for whom sessions are accepted
     *
     * @return string
     */
    public function getReferrerCheck(): string
    {
        return $this->referrerCheck;
    }
    
    /**
     * Gets handler file name
     *
     * @return string
     */
    public function getHandler(): string
    {
        return $this->handlerFile;
    }
    
    /**
     * Gets whether or not session should start automatically
     *
     * @return bool
     */
    public function isAutoStart(): bool
    {
        return $this->autoStart;
    }
}
