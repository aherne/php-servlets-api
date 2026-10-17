<?php

namespace Test\Lucinda\STDOUT\Support\Controllers;

use Lucinda\STDOUT\Restful;

final class InvalidRestfulController implements Restful
{
    public function get(): string
    {
        return "invalid";
    }
}
