<?php

namespace Lucinda\STDOUT\Service;

use Lucinda\STDOUT\Validators\ValidatedRequest;
use Lucinda\MVC\Response\View;
use Lucinda\MVC\ConfigurationException;
use Lucinda\MVC\Application;
use Lucinda\STDOUT\Facets\ResolverInfo;

final class ResponseInfoDetector
{
    private ResolverInfo $resolver;
    private string $contentType;
    
    public function __construct(
        Application $application,
        ValidatedRequest $validatedRequest
    )
    {
        $this->setResolver($application, $validatedRequest);
        $this->setContentType();
    }

    private function setResolver(
        Application $application,
        ValidatedRequest $validatedRequest
    ): void
    {
        $resolver = $application->getResolvers($validatedRequest->getFormat());
        if ($resolver === null) {
            throw new ConfigurationException("Resolver not set for: ".$validatedRequest->getFormat());
        }
        $this->resolver = $resolver;
    }

    public function getResolver(): ResolverInfo
    {
        return $this->resolver;
    }

    private function setContentType(): void
    {
        $charset = $this->resolver->getCharacterEncoding();
        $this->contentType = $this->resolver->getContentType().($charset ? "; charset=".$charset : "");
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }
}