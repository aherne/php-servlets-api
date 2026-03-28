<?php

namespace Lucinda\STDOUT\Validators;

use Lucinda\MVC\Facet;
use Lucinda\MVC\RequestValidator;
use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\Request;

/**
 * Binds information in Request and Application objects in order to detect final route info
 */
final class ValidatedRequest implements Facet, RequestValidator
{
    private string $page;
    private string $format;
    /**
     * @var array<string,string>
     */
    private array $pathParameters;
    /**
     * @var array<string,mixed>
     */
    private array $validatedParameters;

    /**
     * Bootstraps binding process
     * 
     * @param Application $application
     * @param Request $request
     */
    public function __construct(Application $application, Request $request)
    {
        $routeValidator = new RouteValidator($application, $request);
        $this->page = $routeValidator->getUrl();
        $this->pathParameters = $routeValidator->getPathParameters();
        $this->validatedParameters = $routeValidator->getValidParameters();

        $formatValidator = new FormatValidator($application, $this->page);
        $this->format = $formatValidator->getFormat();
    }

    /**
     * Gets final route detected after validation
     * 
     * @return string
     */
    public function getRoute(): string
    {
        return $this->page;
    }

    /**
     * Gets final response format (extension) after validation
     * 
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Gets detected path parameters from route (if they exist)
     * 
     * @return array<string,string>
     */
    public function getPathParameters(): array
    {
        return $this->pathParameters;
    }

    /**
     * Gets parameter validation results for requested page
     * 
     * @return array<string,mixed>
     */
    public function getValidationResults(): array
    {
        return $this->validatedParameters;
    }
}