<?php

namespace Lucinda\STDOUT\Service;

use Lucinda\STDOUT\XmlTags\ResolverInfo;

/**
 * Detects default response content type based on information in ResolverInfo object
 */
final class ContentTypeDetector
{
    private string $contentType;

    /**
     * Bootstraps detection process.
     * 
     * @param ResolverInfo $resolverInfo
     */
    public function __construct(ResolverInfo $resolverInfo)
    {
        $this->setContentType($resolverInfo);
    }

    /**
     * Sets default content type
     * 
     * @param ResolverInfo $resolverInfo
     */
    private function setContentType(ResolverInfo $resolverInfo): void
    {
        $charset = $resolverInfo->getCharacterEncoding();
        $this->contentType = $resolverInfo->getContentType().($charset ? "; charset=".$charset : "");
    }

    /**
     * Gets detected content type
     * 
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }
}
