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
 * Parses and exposes the XML configuration used by the HTTP MVC runtime
 *
 * In addition to the application, resolver, and route information supplied by
 * the base MVC package, this specialization parses optional session and cookie
 * settings and uses HTTP-aware route and resolver value objects.
 *
 * Configuration is loaded eagerly during construction and remains immutable
 * for the lifetime of the application object.
 *
 * @see RouteInfo
 * @see ResolverInfo
 * @see SessionOptions
 * @see CookiesOptions
 */
final class Application extends \Lucinda\MVC\Application
{
    /**
     * Parsed session settings, or null when no `session` tag is configured
     */
    private ?SessionOptions $sessionOptions = null;

    /**
     * Parsed cookie defaults, or null when no `cookies` tag is configured
     */
    private ?CookiesOptions $cookiesOptions = null;

    /**
     * Loads the application document and parses HTTP-specific options
     *
     * @param string $xmlFilePath Path to the root XML configuration document
     * @throws ConfigurationException If the document or required application configuration is invalid
     */
    public function __construct(string $xmlFilePath)
    {
        parent::__construct($xmlFilePath);
        $this->setSessionOptions();
        $this->setCookieOptions();
    }

    /**
     * Parses routes into HTTP-aware RouteInfo objects
     */
    protected function setRoutes(): void
    {
        $list = new RoutesList(RouteInfo::class);
        $this->routes = $list->convert($this->reader->getTag("routes"));
    }

    /**
     * Parses resolvers into HTTP-aware ResolverInfo objects
     */
    protected function setResolvers(): void
    {
        $list = new ResolversList(ResolverInfo::class);
        $this->formats = $list->convert($this->reader->getTag("resolvers"));
    }

    /**
     * Parses optional session settings
     */
    private function setSessionOptions(): void
    {
        if (!$this->reader->hasTag("session")) {
            return; // it is ok not to have this tag
        }
        $this->sessionOptions = new SessionOptions($this->reader->getTag("session"));
    }

    /**
     * Parses optional default cookie settings
     */
    private function setCookieOptions(): void
    {
        if (!$this->reader->hasTag("cookies")) {
            return; // it is ok not to have this tag
        }
        $this->cookiesOptions = new CookiesOptions($this->reader->getTag("cookies"));
    }

    /**
     * Gets configured PHP session settings
     *
     * @return SessionOptions|null Parsed settings, or null when `session` is absent
     */
    public function getSessionOptions(): ?SessionOptions
    {
        return $this->sessionOptions;
    }

    /**
     * Gets defaults applied when response cookies are created
     *
     * @return CookiesOptions|null Parsed defaults, or null when `cookies` is absent
     */
    public function getCookieOptions(): ?CookiesOptions
    {
        return $this->cookiesOptions;
    }

    /**
     * Gets every configured route for exact and parameterized matching
     *
     * @return array<string,RouteInfo> Routes keyed by their configured identifiers
     */
    public function getAllRoutes(): array
    {
        return $this->routes;
    }
}
