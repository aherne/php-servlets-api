<?php

namespace Test\Lucinda\STDOUT\Support\Controllers;

use Lucinda\MVC\Response\View;
use Lucinda\STDOUT\Restful;

final class RestfulController implements Restful
{
    public function get(): View
    {
        return new View(["method" => "GET"], "resource");
    }

    public function delete(): void
    {
    }
}
