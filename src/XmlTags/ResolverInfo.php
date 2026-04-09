<?php

namespace Lucinda\STDOUT\XmlTags;

use Lucinda\MVC\XmlTags\ResolverInfo as AbstractResolverInfo;
use Lucinda\MVC\XmlReader\Element;
use Lucinda\MVC\XmlReader\Exception;

/**
 * Overrides default ResolverInfo by also covering "content_type" & "charset" XML attributes by default
 */
final class ResolverInfo extends AbstractResolverInfo
{
    private string $contentType;
    private string $characterEncoding;

    /**
     * Bootstraps detection process
     * 
     * @param Element $element
     */
    public function __construct(Element $element)
    {
        parent::__construct($element);

        $attributes = $element->getAttributes();
        $this->setContentType($attributes);
        $this->setCharacterEncoding($attributes);
    }
    
    /**
     * Sets content type
     * 
     * @param array<string,string> $attributes
     * @throws Exception If XML is misconfigured.
     */
    private function setContentType(array $attributes): void
    {
        if (empty($attributes["content_type"])) {
            throw new Exception("Attribute 'content_type' is mandatory for 'resolver' tag");
        }
        $this->contentType = $attributes["content_type"];
    }

    /**
     * Gets content type
     *
     * @return  string
     * @example application/json
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }
    
    /**
     * Sets character encoding (if present)
     * 
     * @param array<string,string> $attributes
     * @throws Exception If XML is misconfigured.
     */
    private function setCharacterEncoding(array $attributes): void
    {
        $this->characterEncoding = $attributes["charset"]??"";
    }

    /**
     * Gets character encoding (charset)
     *
     * @return string
     */
    public function getCharacterEncoding(): string
    {
        return $this->characterEncoding;
    }
}
