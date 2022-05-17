<?php

namespace Lucinda\STDOUT\Cookies;

/**
 * Sets options to create session with based on contents of XML tag "session"
 */
class Options
{
    private string $path;
    private string $domain;
    private bool $isSecuredByHTTPS;
    private bool $isSecuredByHTTPheaders;

    /**
     * Saves session options based on XML tag "cookie"
     *
     * @param \SimpleXMLElement $info
     */
    public function __construct(\SimpleXMLElement $info)
    {
        $this->path = (string) $info["path"];
        $this->domain = (string) $info["domain"];
        $this->isSecuredByHTTPS = (bool) $info["https_only"];
        $this->isSecuredByHTTPheaders = (bool) $info["headers_only"];
    }

    /**
     * Gets path on the server in which the cookie will be available on.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Gets (sub)domain that the cookie is available to.
     *
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * Gets whether cookies are available only if protocol is HTTPS
     *
     * @return bool
     */
    public function isSecuredByHTTPS(): bool
    {
        return $this->isSecuredByHTTPS;
    }

    /**
     * Gets whether cookies are not available to client via JavaScript
     *
     * @return bool
     */
    public function isSecuredByHTTPheaders(): bool
    {
        return $this->isSecuredByHTTPheaders;
    }
}
