<?php

namespace Lucinda\STDOUT\Validators;

use Lucinda\STDOUT\Request;
use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\PathNotFoundException;
use Lucinda\STDOUT\MethodNotAllowedException;

/**
 * Matches an HTTP request to a configured route and validates its parameters
 *
 * Exact route identifiers are preferred. When no exact route exists, route
 * identifiers containing `(name)` placeholders are matched in configuration
 * order and their path values are extracted. The selected route's optional
 * request method and parameter rules are then enforced.
 *
 * Path parameters override request parameters of the same name before custom
 * ParameterValidator implementations are executed.
 */
final class RouteValidator
{
    /**
     * Matched route identifier, including placeholder syntax when applicable
     */
    private string $url;

    /**
     * @var array<string,string> Raw values extracted from route placeholders
     */
    private array $pathParameters=[];

    /**
     * @var array<string,mixed> Successful parameter validation results
     */
    private array $validParameters=[];

    /**
     * Matches the route and validates its method and parameter constraints
     *
     * @param Application $application Parsed route configuration
     * @param Request $request Current immutable HTTP request
     * @throws PathNotFoundException If no route matches the request path
     * @throws MethodNotAllowedException If the matched route rejects the request method
     * @throws ValidationFailedException If a mandatory parameter is absent or validation returns null
     */
    public function __construct(Application $application, Request $request)
    {
        $this->validateUrl($application, $request);
        $this->validateRequestMethod($application, $request);
        $this->validateParameters($application, $request);
    }

    /**
     * Matches the requested page and extracts route placeholder values
     *
     * An empty page uses the application's default route. Parameterized routes
     * are evaluated only when no exact route identifier matches.
     *
     * @param Application $application Parsed route configuration
     * @param Request $request Current immutable HTTP request
     * @throws PathNotFoundException If no exact or parameterized route matches
     */
    private function validateUrl(Application $application, Request $request): void
    {
        $url = $request->getURI()->getPage();
        if ($url=="") {
            $url = $application->getApplicationInfo()->getDefaultRoute();
        }
        if ($application->getRoutes($url)===null) {
            $matchFound = false;
            $routes = $application->getAllRoutes();
            foreach ($routes as $route) {
                if (str_contains($route->getID(), "(")) {
                    $matches = [];
                    preg_match_all("/(\(([^)]+)\))/", $route->getID(), $matches);
                    $names = $matches[2];
                    $pattern = "/^".str_replace($matches[1], "([^\/]+)", str_replace("/", "\/", $route->getID()))."$/";
                    $results = [];
                    if (preg_match_all($pattern, $url, $results)==1) {
                        $parameters = [];
                        foreach ($results as $i=>$item) {
                            if ($i==0) {
                                continue;
                            }
                            $parameters[$names[$i-1]]=$item[0];
                        }
                        $this->pathParameters = $parameters;
                        $url = $route->getID();
                        $matchFound = true;
                        break;
                    }
                }
            }
            if (!$matchFound) {
                throw new PathNotFoundException("Route could not be matched to routes.route tag @ XML: ".$url);
            }
        }
        $this->url = $url;
    }

    /**
     * Enforces the optional HTTP method constraint of the matched route
     *
     * @param Application $application Parsed route configuration
     * @param Request $request Current immutable HTTP request
     * @throws MethodNotAllowedException If the configured method differs from the request method
     */
    private function validateRequestMethod(Application $application, Request $request): void
    {
        $validRequestMethod = $application->getRoutes($this->url)->getValidRequestMethod();
        if ($validRequestMethod && ($validRequestMethod->value != $request->getMethod()->value)) {
            throw new MethodNotAllowedException("Route allows only request method: ".$validRequestMethod->value);
        }
    }

    /**
     * Validates configured parameters for the matched route
     *
     * @param Application $application Parsed route configuration
     * @param Request $request Current immutable HTTP request
     * @throws ValidationFailedException If a mandatory value is absent or a validator returns null
     * @throws \Throwable If a configured validator cannot be constructed or executed
     */
    private function validateParameters(Application $application, Request $request): void
    {
        // get request parameters
        $parameters = $request->parameters();
        foreach ($this->pathParameters as $k=>$v) {
            $parameters[$k] = $v;
        }

        $validators = $application->getRoutes($this->url)->getValidParameters();
        foreach ($validators as $parameterName=>$class) {
            if (!isset($parameters[$parameterName])) {
                if ($class->isMandatory()) {
                    throw new ValidationFailedException("Parameter has no value: ".$parameterName);
                } else {
                    continue;
                }
            }

            $className = $class->getValidator();
            $object = new $className();
            $result = $object->validate($parameters[$parameterName]);
            if ($result===null) {
                throw new ValidationFailedException("Parameter failed validation: ".$parameterName);
            }
            $this->validParameters[$parameterName] = $result;
        }
    }

    /**
     * Gets the matched route identifier
     *
     * @return string Exact configured route ID, including any placeholder syntax
     */
    public function getUrl(): string
    {
        return $this->url;
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
    public function getValidParameters(): array
    {
        return $this->validParameters;
    }
}
