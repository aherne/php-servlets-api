<?php

namespace Lucinda\STDOUT\XmlTags;

use Lucinda\MVC\XmlReader\Element;
use Lucinda\MVC\XmlReader\Exception;

/**
 * Detects default cookie options from XML tag <cookie>
 */
final class CookiesOptions
{
    private string $path;
    private string $domain;
    private bool $isSecuredByHTTPS;
    private bool $isSecuredByHTTPheaders;

    /**
     * Saves cookie options based on XML tag "cookie"
     *
     * @param Element $element
     */
    public function __construct(Element $element)
    {
        $info = $element->getAttributes();
        $this->setStringOptions($info);
        $this->setBooleanOptions($info);
    }

    /**
     * Sets cookie options of string type
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
     * Sets cookie options of boolean type
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
