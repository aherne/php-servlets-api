<?php

namespace Test\Lucinda\STDOUT\Support\Resolvers;

use Lucinda\MVC\Response\View;
use Lucinda\MVC\Response\ViewResolver;

final class PhtmlResolver implements ViewResolver
{
    public function resolve(View $view): string
    {
        $data = $view->getData();
        ob_start();
        include $view->getFile();
        return (string) ob_get_clean();
    }
}
