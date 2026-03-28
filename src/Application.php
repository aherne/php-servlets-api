<?php

namespace Lucinda\STDOUT;

use Lucinda\STDOUT\XmlTags\SessionOptions;
use Lucinda\STDOUT\XmlTags\CookiesOptions;
use Lucinda\STDOUT\XmlTags\RouteInfo;
use Lucinda\STDOUT\XmlTags\ResolverInfo;
use Lucinda\MVC\ConfigurationException;
use Lucinda\MVC\XmlTagsLists\ResolversList;
use Lucinda\MVC\XmlTagsLists\RoutesList;

/**
 * Compiles information about application.
 */
final class Application extends \Lucinda\MVC\Application
{
    private ?SessionOptions $sessionOptions = null;
    private ?CookiesOptions $cookiesOptions = null;

    /**
     * Populates attributes based on an XML file
     *
     * @param  string $xmlFilePath XML file url
     * @throws ConfigurationException If xml content has failed validation.
     */
    public function __construct(string $xmlFilePath)
    {
        parent::__construct($xmlFilePath);
        $this->setSessionOptions();
        $this->setCookieOptions();
    }

    /**
     * Sets customized routes lists
     */
    protected function setRoutes(): void
    {
        $list = new RoutesList(RouteInfo::class);
        $this->routes = $list->convert($this->reader->getTag("routes"));
    }

        /**
     * Sets view resolvers info based on contents of "resolvers" XML tag
     *
     * @throws XmlException If xml content has failed validation.
     */
    protected function setResolvers(): void
    {
        $list = new ResolversList(ResolverInfo::class);
        $this->formats = $list->convert($this->reader->getTag("resolvers"));
    }

    /**
     * Sets options to start session with based on "session" XML tag
     */
    private function setSessionOptions(): void
    {
        if (!$this->reader->hasTag("session")) {
            return; // it is ok not to have this tag
        }
        $this->sessionOptions = new SessionOptions($this->reader->getTag("session"));
    }

    /**
     * Sets options to create cookies with based on "cookies" XML tag
     */
    private function setCookieOptions(): void
    {
        if (!$this->reader->hasTag("cookies")) {
            return; // it is ok not to have this tag
        }
        $this->cookiesOptions = new CookiesOptions($this->reader->getTag("cookies"));
    }

    /**
     * Gets  options to start session with based on "session" XML tag
     *
     * @return SessionOptions|NULL
     */
    public function getSessionOptions(): ?SessionOptions
    {
        return $this->sessionOptions;
    }

    /**
     * Gets options to create cookies with based on "cookies" XML tag
     *
     * @return CookiesOptions|NULL
     */
    public function getCookieOptions(): ?CookiesOptions
    {
        return $this->cookiesOptions;
    }

    /**
     * Gets all routes for later request validation
     */
    public function getAllRoutes(): array
    {
        return $this->routes;
    }
}
