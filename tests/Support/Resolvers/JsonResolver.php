<?php

namespace Test\Lucinda\STDOUT\Support\Resolvers;

use Lucinda\MVC\Response\View;
use Lucinda\MVC\Response\ViewResolver;

final class JsonResolver implements ViewResolver
{
    public function resolve(View $view): string
    {
        return json_encode($view->getData(), JSON_THROW_ON_ERROR);
    }
}
