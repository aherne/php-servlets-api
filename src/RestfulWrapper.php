<?php

namespace Lucinda\STDOUT;

use Lucinda\MVC\Response\View;

/**
 * Dispatches a RESTful controller according to the current HTTP request method
 *
 * The enum value exposed by Request is used as the controller method name. Only
 * public callable methods with no required arguments are accepted, and their
 * result must be a View or null so FrontController can process it consistently.
 *
 * Dispatch occurs eagerly during construction; getResult() only exposes the
 * already validated controller result.
 *
 * @see Restful
 * @see FrontController
 */
final class RestfulWrapper
{
    /**
     * View returned by the selected controller method, or null for no view
     */
    private ?View $result;

    /**
     * Selects, validates, and executes the request-method handler
     *
     * @param Restful $controller RESTful controller selected for the current route
     * @param Request $request Current immutable HTTP request
     * @throws MethodNotAllowedException If the controller has no public callable handler for the request method
     * @throws \LogicException If the handler requires arguments or returns a value other than View or null
     */
    public function __construct(Restful $controller, Request $request)
    {
        $methodName = $request->getMethod()->value;
        $callback = [$controller, $methodName];
        $this->validateMethod($controller, $methodName, $callback);
        $this->validateNumberOfArguments($controller, $methodName);
        $result = $callback();
        $this->validateResult($controller, $methodName, $result);
        $this->result = $result;
    }

    /**
     * Verifies that the request-method handler exists and is publicly callable
     *
     * @param Restful $controller RESTful controller selected for the route
     * @param string $methodName HTTP method used as the handler name
     * @param array{0:Restful,1:string} $callback Candidate controller callback
     * @throws MethodNotAllowedException If the candidate is absent or inaccessible
     */
    private function validateMethod(
        Restful $controller,
        string $methodName,
        array $callback
    ): void
    {
        if (!method_exists($controller, $methodName) || !is_callable($callback)) {
            throw new MethodNotAllowedException($methodName);
        }
    }

    /**
     * Verifies that dispatch does not require arguments from the wrapper
     *
     * Optional parameters are permitted because PHP can invoke the handler
     * without supplying them.
     *
     * @param Restful $controller RESTful controller selected for the route
     * @param string $methodName Validated controller handler name
     * @throws \LogicException If the handler declares one or more required parameters
     */
    private function validateNumberOfArguments(
        Restful $controller,
        string $methodName
    ): void
    {
        $method = new \ReflectionMethod($controller, $methodName);
        if ($method->getNumberOfRequiredParameters() > 0) {
            throw new \LogicException(
                $controller::class."::".$methodName."() must not require arguments"
            );
        }
    }

    /**
     * Verifies that the handler result can participate in MVC view resolution
     *
     * @param Restful $controller RESTful controller selected for the route
     * @param string $methodName Executed controller handler name
     * @param mixed $result Value returned by the handler
     * @throws \LogicException If the result is neither View nor null
     */
    private function validateResult(
        Restful $controller,
        string $methodName,
        mixed $result
    ): void
    {
        if ($result !== null && !$result instanceof View) {
            throw new \LogicException(
                $controller::class."::".$methodName.
                "() must return View or null"
            );
        }
    }

    /**
     * Gets the validated result returned by the selected controller handler
     *
     * @return View|null Handler view, or null when the handler produced no view
     */
    public function getResult(): ?View
    {
        return $this->result;
    }
}
