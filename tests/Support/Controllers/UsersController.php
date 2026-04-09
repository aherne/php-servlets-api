<?php

namespace Test\Lucinda\STDOUT\Support\Controllers;

use Lucinda\MVC\Controller\ViewAware;
use Lucinda\MVC\Response\View;

final class UsersController implements ViewAware
{
    public function run(): View
    {
        return new View(["message" => "Hello from controller"], "hello");
    }
}
