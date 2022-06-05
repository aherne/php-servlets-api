<?php

namespace Lucinda\STDOUT\EventListeners;

use Lucinda\STDOUT\EventListeners\Validators\RouteValidator;
use Lucinda\STDOUT\EventListeners\Validators\FormatValidator;

/**
 * Validates request data based on Application and Request objects and saves results to Attributes
 */
class RequestValidator extends Request
{
    /**
     * Performs request validation
     *
     * @throws \Lucinda\MVC\ConfigurationException
     */
    public function run(): void
    {
        $routeValidator = new RouteValidator($this->application, $this->request);
        $this->attributes->setValidPage($routeValidator->getUrl());
        $this->attributes->setPathParameters($routeValidator->getPathParameters());
        $this->attributes->setValidParameters($routeValidator->getValidParameters());

        $formatValidator = new FormatValidator($this->application, $routeValidator->getUrl());
        $this->attributes->setValidFormat($formatValidator->getFormat());
    }
}
