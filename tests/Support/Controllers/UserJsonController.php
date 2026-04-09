<?php

namespace Test\Lucinda\STDOUT\Support\Controllers;

use Lucinda\MVC\Controller\ViewAware;
use Lucinda\MVC\Response\View;
use Lucinda\STDOUT\Validators\ValidatedRequest;

final class UserJsonController implements ViewAware
{
    public function __construct(private ValidatedRequest $validatedRequest)
    {
    }

    public function run(): View
    {
        return new View([
            "route" => $this->validatedRequest->getRoute(),
            "path" => $this->validatedRequest->getPathParameters(),
            "validated" => $this->validatedRequest->getValidationResults(),
        ]);
    }
}
