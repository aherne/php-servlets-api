<?php

namespace Test\Lucinda\STDOUT;

use Lucinda\MVC\Response\View;
use Lucinda\STDOUT\MethodNotAllowedException;
use Lucinda\STDOUT\Request;
use Lucinda\STDOUT\RestfulWrapper;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;
use Test\Lucinda\STDOUT\Support\Controllers\InvalidRestfulController;
use Test\Lucinda\STDOUT\Support\Controllers\RequiredArgumentRestfulController;
use Test\Lucinda\STDOUT\Support\Controllers\RestfulController;

final class RestfulWrapperTest
{
    public function getResult()
    {
        $request = $this->request("GET");
        $controller = new RestfulController();

        $result = (new RestfulWrapper($controller, $request))->getResult();

        return [
            (new Objects($result))->assertInstanceOf(View::class),
            (new Arrays($result->getData()))->assertIdentical(["method" => "GET"]),
            (new Strings($result->getFile()))->assertEquals("resource"),
        ];
    }

    public function nullResult()
    {
        $controller = new RestfulController();

        $result = (new RestfulWrapper($controller, $this->request("DELETE")))->getResult();

        return (new Arrays([$result]))->assertIdentical([null]);
    }

    public function unsupportedMethod()
    {
        $controller = new RestfulController();

        try {
            new RestfulWrapper($controller, $this->request("POST"));
        } catch (MethodNotAllowedException $exception) {
            return (new Strings($exception->getMessage()))->assertEquals("POST");
        }

        throw new \RuntimeException("Missing REST handler did not produce MethodNotAllowedException");
    }

    public function requiredArguments()
    {
        $controller = new RequiredArgumentRestfulController();

        try {
            new RestfulWrapper($controller, $this->request("GET"));
        } catch (\LogicException $exception) {
            return (new Strings($exception->getMessage()))->assertEquals(
                $controller::class."::GET() must not require arguments"
            );
        }

        throw new \RuntimeException("REST handler arguments did not produce LogicException");
    }

    public function invalidResult()
    {
        $controller = new InvalidRestfulController();

        try {
            new RestfulWrapper($controller, $this->request("GET"));
        } catch (\LogicException $exception) {
            return (new Strings($exception->getMessage()))->assertEquals(
                $controller::class."::GET() must return View or null"
            );
        }

        throw new \RuntimeException("Invalid REST handler result did not produce LogicException");
    }

    private function request(string $method): Request
    {
        return TestHelper::request(TestHelper::requestServer(["REQUEST_METHOD" => $method]));
    }
}
