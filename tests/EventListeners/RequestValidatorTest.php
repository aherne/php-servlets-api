<?php

namespace Test\Lucinda\STDOUT\EventListeners;

use Lucinda\STDOUT\EventListeners\RequestValidator;
use Lucinda\STDOUT\Attributes;
use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\Request;
use Lucinda\UnitTest\Result;
use Lucinda\STDOUT\Session;
use Lucinda\STDOUT\Cookies;

class RequestValidatorTest
{
    public function run()
    {
        $attributes = new Attributes("tests/mocks/events");
        $_SERVER = [
            'HTTP_HOST' => 'www.test.local',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:74.0) Gecko/20100101 Firefox/74.0',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.5',
            'HTTP_ACCEPT_ENCODING' => 'gzip, deflate',
            'HTTP_CONNECTION' => 'keep-alive',
            'HTTP_COOKIE' => '_ga=GA1.2.1051007502.1535802299',
            'HTTP_UPGRADE_INSECURE_REQUESTS' => '1',
            'HTTP_CACHE_CONTROL' => 'max-age=0',
            'SERVER_ADMIN' => '',
            'SERVER_SOFTWARE' => 'Apache/2.4.29 (Ubuntu)',
            'SERVER_NAME' => 'www.documentation.local',
            'SERVER_ADDR' => '127.0.0.1',
            'SERVER_PORT' => '80',
            'REMOTE_ADDR' => '127.0.0.1',
            'REMOTE_PORT' => '59300',
            'REQUEST_SCHEME' => 'http',
            'REQUEST_URI' => '/user/lucian',
            'REQUEST_METHOD' => 'GET',
            'DOCUMENT_ROOT' => '/var/www/html/documentation',
            'SCRIPT_FILENAME' => '/var/www/html/documentation/index.php',
            'QUERY_STRING' =>''
        ];
        $validator = new RequestValidator($attributes, new Application(dirname(__DIR__)."/mocks/configuration.xml"), new Request(), new Session(), new Cookies());
        $validator->run();

        $results = [];

        $results[] = new Result($attributes->getValidPage()=="user/(name)");
        $results[] = new Result($attributes->getPathParameters()==["name"=>"lucian"]);
        $results[] = new Result($attributes->getValidParameters()==["name"=>1]);
        $results[] = new Result($attributes->getValidFormat()=="json");

        return $results;
    }
}
