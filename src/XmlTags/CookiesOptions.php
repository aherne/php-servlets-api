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
    /**
     * Default cookie path
     */
    private string $path;
    /**
     * Default cookie domain
     */
    private string $domain;
    /**
     * Whether cookies are restricted to HTTPS
     */
    private bool $isSecuredByHTTPS;
    /**
     * Whether cookies are hidden from browser scripting
     */
    private bool $isSecuredByHTTPheaders;

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
     * @return string Configured cookie path
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Gets the domain on which created cookies are available
     *
     * @return string Configured cookie domain
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * Checks whether created cookies require HTTPS
     *
     * @return bool Configured secure-cookie flag
     */
    public function isSecuredByHTTPS(): bool
    {
        return $this->isSecuredByHTTPS;
    }

    /**
     * Checks whether created cookies are hidden from browser scripting
     *
     * @return bool Configured HTTP-only cookie flag
     */
    public function isSecuredByHTTPheaders(): bool
    {
        return $this->isSecuredByHTTPheaders;
    }
}
