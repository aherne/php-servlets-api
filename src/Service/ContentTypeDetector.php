<?php

namespace Lucinda\STDOUT\Service;

use Lucinda\STDOUT\Facets\ResolverInfo;

final class ContentTypeDetector
{
    private string $contentType;

    public function __construct(ResolverInfo $resolverInfo)
    {
        $this->setContentType($resolverInfo);
    }

    private function setContentType(ResolverInfo $resolverInfo): void
    {
        $charset = $resolverInfo->getCharacterEncoding();
        $this->contentType = $resolverInfo->getContentType().($charset ? "; charset=".$charset : "");
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }
}
