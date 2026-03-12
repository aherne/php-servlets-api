<?php

namespace Lucinda\STDOUT\Validators;

use Lucinda\MVC\Facet;
use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\Request;

final class ValidatedRequest implements Facet
{
    private string $page;
    private string $format;
    private array $pathParameters;
    private array $validatedParameters;

    public function __construct(Application $application, Request $request)
    {
        $routeValidator = new RouteValidator($application, $request);
        $this->page = $routeValidator->getUrl();
        $this->pathParameters = $routeValidator->getPathParameters();
        $this->validatedParameters = $routeValidator->getValidParameters();

        $formatValidator = new FormatValidator($application, $this->page);
        $this->format = $formatValidator->getFormat();
    }

    public function getPage(): string
    {
        return $this->page;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getPathParameters(): array
    {
        return $this->pathParameters;
    }

    public function getValidatedParameters(): array
    {
        return $this->validatedParameters;
    }
}