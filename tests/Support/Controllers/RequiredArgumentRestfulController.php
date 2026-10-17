<?php

namespace Test\Lucinda\STDOUT\Support\Controllers;

use Lucinda\MVC\Response\View;
use Lucinda\STDOUT\Restful;

final class RequiredArgumentRestfulController implements Restful
{
    public function get(string $id): ?View
    {
        return null;
    }
}
