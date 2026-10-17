<?php

namespace Lucinda\STDOUT\XmlTags;

use Lucinda\MVC\XmlTags\ResolverInfo as AbstractResolverInfo;
use Lucinda\MVC\XmlReader\Element;
use Lucinda\MVC\XmlReader\Exception;

/**
 * Extends MVC resolver metadata with HTTP representation information
 *
 * Every resolver must declare a `content_type`. The optional `charset` is used
 * by ContentTypeDetector when building the response Content-Type header.
 */
final class ResolverInfo extends AbstractResolverInfo
{
    /**
     * Required response media type
     */
    private string $contentType;
    /**
     * Optional response character encoding
     */
    private string $characterEncoding;

    /**
     * Parses base resolver metadata and HTTP-specific attributes
     *
     * @param Element $element Parsed `resolver` element
     * @throws Exception If `content_type` is absent or empty
     */
    public function __construct(Element $element)
    {
        parent::__construct($element);

        $attributes = $element->getAttributes();
        $this->setContentType($attributes);
        $this->setCharacterEncoding($attributes);
    }

    /**
     * Validates and stores the required response media type
     *
     * @param array<string,string> $attributes Resolver attributes
     * @throws Exception If `content_type` is absent or empty
     */
    private function setContentType(array $attributes): void
    {
        if (empty($attributes["content_type"])) {
            throw new Exception("Attribute 'content_type' is mandatory for 'resolver' tag");
        }
        $this->contentType = $attributes["content_type"];
    }

    /**
     * Gets the configured response media type
     *
     * @return string Media type such as `application/json`
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * Stores the optional response character encoding
     *
     * @param array<string,string> $attributes Resolver attributes
     */
    private function setCharacterEncoding(array $attributes): void
    {
        $this->characterEncoding = $attributes["charset"]??"";
    }

    /**
     * Gets the configured response character encoding
     *
     * @return string Charset value, or an empty string when not configured
     */
    public function getCharacterEncoding(): string
    {
        return $this->characterEncoding;
    }
}
