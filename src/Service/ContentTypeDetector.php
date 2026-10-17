<?php

namespace Lucinda\STDOUT\Service;

use Lucinda\STDOUT\XmlTags\ResolverInfo;

/**
 * Builds the HTTP Content-Type header value for a configured resolver
 *
 * The resolver's media type is used verbatim. When a character encoding is
 * configured, it is appended as a `charset` parameter.
 */
final class ContentTypeDetector
{
    /**
     * Complete Content-Type header value
     */
    private string $contentType;

    /**
     * Builds a content type from resolver metadata
     *
     * @param ResolverInfo $resolverInfo Resolver selected for the response format
     */
    public function __construct(ResolverInfo $resolverInfo)
    {
        $this->setContentType($resolverInfo);
    }

    /**
     * Combines the configured media type and optional character encoding
     *
     * @param ResolverInfo $resolverInfo Resolver selected for the response format
     */
    private function setContentType(ResolverInfo $resolverInfo): void
    {
        $charset = $resolverInfo->getCharacterEncoding();
        $this->contentType = $resolverInfo->getContentType().($charset ? "; charset=".$charset : "");
    }

    /**
     * Gets the complete response Content-Type value
     *
     * @return string Media type with an optional charset parameter
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }
}
