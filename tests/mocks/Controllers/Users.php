<?php

namespace Test\Lucinda\STDOUT\mocks\Controllers;

use Lucinda\STDOUT\Controller;

class Users extends Controller
{
    public function run(): void
    {
        $this->response->view()["test"] = "me";
    }
}
