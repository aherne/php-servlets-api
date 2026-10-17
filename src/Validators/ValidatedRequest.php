<?php

namespace Lucinda\STDOUT\Validators;

use Lucinda\MVC\Facet;
use Lucinda\MVC\RequestValidator;
use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\Request;

/**
 * Exposes the validated routing state of an HTTP request as an MVC facet
 *
 * Construction performs route, method, parameter, and response-format
 * validation. Controllers and listeners can inject this object to consume the
 * selected route and validated values without repeating request parsing.
 */
final class ValidatedRequest implements Facet, RequestValidator
{
    /**
     * Matched configured route identifier
     */
    private string $page;
    /**
     * Selected and resolver-backed response format
     */
    private string $format;

    /**
     * @var array<string,string> Raw values extracted from route placeholders
     */
    private array $pathParameters;

    /**
     * @var array<string,mixed> Successful custom parameter validation results
     */
    private array $validatedParameters;

    /**
     * Validates routing state and selects the response format
     *
     * @param Application $application Parsed application, route, and resolver configuration
     * @param Request $request Current immutable HTTP request
     * @throws \Lucinda\STDOUT\PathNotFoundException If no configured route matches the request path
     * @throws \Lucinda\STDOUT\MethodNotAllowedException If the route rejects the request method
     * @throws ValidationFailedException If a configured parameter fails validation
     * @throws \Lucinda\MVC\XmlReader\Exception If the selected format has no resolver
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
     * Gets the matched route identifier
     *
     * @return string Exact configured route ID, including placeholder syntax
     */
    public function getRoute(): string
    {
        return $this->page;
    }

    /**
     * Gets the selected response format
     *
     * @return string Resolver format identifier selected for the route
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Gets raw values extracted from route placeholders
     *
     * @return array<string,string> Values keyed by placeholder name
     */
    public function getPathParameters(): array
    {
        return $this->pathParameters;
    }

    /**
     * Gets successful parameter validation results
     *
     * @return array<string,mixed> Validator results keyed by parameter name
     */
    public function getValidationResults(): array
    {
        return $this->validatedParameters;
    }
}
