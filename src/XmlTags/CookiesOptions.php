<?php

namespace Lucinda\STDOUT\XmlTags;

use Lucinda\MVC\XmlReader\Element;
use Lucinda\MVC\XmlReader\Exception;

/**
 * Parses default cookie attributes from the XML `cookies` element
 *
 * Path and domain values are retained as configured. The `https_only` and
 * `headers_only` flags accept only 0 or 1 and control the secure and HTTP-only
 * arguments supplied to setcookie().
 */
final class CookiesOptions
{
    private ?string $sameSite = null;

    /**
     * Default cookie path
     */
    private ?string $path = null;
    /**
     * Default cookie domain
     */
    private ?string $domain = null;
    /**
     * Whether cookies are restricted to HTTPS
     */
    private ?bool $isSecuredByHTTPS = null;
    /**
     * Whether cookies are hidden from browser scripting
     */
    private ?bool $isSecuredByHTTPheaders = null;

    /**
     * Parses cookie defaults from an XML element
     *
     * @param Element $element Parsed `cookies` element
     * @throws Exception If a boolean attribute is neither 0 nor 1
     */
    public function __construct(Element $element)
    {
        $info = $element->getAttributes();
        $this->setStringOptions($info);
        $this->setBooleanOptions($info);
        $this->setSameSite($info);
    }

    /**
     * Assigns configured path and domain values
     *
     * @param array<string,string> $info Attributes of the `cookies` element
     */
    private function setStringOptions(array $info): void
    {
        $options = [
            "path"=>"path",
            "domain"=>"domain"
        ];
        foreach ($options as $field=>$column) {
            if (isset($info[$column])) {
                $this->$field = $info[$column];
            }
        }
    }

    /**
     * Validates and assigns secure and HTTP-only flags
     *
     * @param array<string,string> $info Attributes of the `cookies` element
     * @throws Exception If a boolean attribute is neither 0 nor 1
     */
    private function setBooleanOptions(array $info): void
    {
        $options = [
            "isSecuredByHTTPS"=>"https_only",
            "isSecuredByHTTPheaders"=>"headers_only"
        ];
        foreach ($options as $field=>$column) {
            if (isset($info[$column])) {
                if ($info[$column]!=1 && $info[$column]!=0) {
                    throw new Exception("Field ".$column." can only have 1 or 0 value");
                }
                $this->$field = (bool) $info[$column];
            }
        }
    }

    /**
     * Gets the URL path on which created cookies are available
     *
     * @return string|null Configured cookie path
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Gets the domain on which created cookies are available
     *
     * @return string|null Configured cookie domain
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * Checks whether created cookies require HTTPS
     *
     * @return bool|null Configured secure-cookie flag
     */
    public function isSecuredByHTTPS(): ?bool
    {
        return $this->isSecuredByHTTPS;
    }

    /**
     * Checks whether created cookies are hidden from browser scripting
     *
     * @return bool|null Configured HTTP-only cookie flag
     */
    public function isSecuredByHTTPheaders(): ?bool
    {
        return $this->isSecuredByHTTPheaders;
    }
    /**
     * Parses the optional SameSite policy without depending on the security API.
     *
     * @param array<string,string> $info XML attributes
     * @throws Exception If the policy is unsupported
     */
    private function setSameSite(array $info): void
    {
        if (!isset($info["same_site"])) {
            return;
        }
        if (!in_array($info["same_site"], ["Lax", "Strict", "None"], true)) {
            throw new Exception("Field same_site must be Lax, Strict or None");
        }
        $this->sameSite = $info["same_site"];
    }

    /**
     * Gets the configured SameSite policy, or null when omitted.
     */
    public function getSameSite(): ?string
    {
        return $this->sameSite;
    }

}
