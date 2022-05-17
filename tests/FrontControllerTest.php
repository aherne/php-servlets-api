<?php

namespace Test\Lucinda\STDOUT;

use Lucinda\STDOUT\FrontController;
use Lucinda\STDOUT\EventType;
use Lucinda\UnitTest\Result;
use Test\Lucinda\STDOUT\mocks\TestAttributes;
use Test\Lucinda\STDOUT\mocks\EventListeners\StartTracker;
use Test\Lucinda\STDOUT\mocks\EventListeners\EndTracker;

class FrontControllerTest
{
    private $object;
    private $attributes;

    public function __construct()
    {
        $this->attributes = new TestAttributes(__DIR__."/mocks/events");
        $this->object = new FrontController(__DIR__."/mocks/configuration.xml", $this->attributes);
    }

    public function addEventListener()
    {
        $this->object->addEventListener(EventType::START, StartTracker::class);
        $this->object->addEventListener(EventType::END, EndTracker::class);
        return new Result(true);
    }


    public function run()
    {
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
            'REQUEST_URI' => '/users',
            'REQUEST_METHOD' => 'GET',
            'DOCUMENT_ROOT' => '/var/www/html/documentation',
            'SCRIPT_FILENAME' => '/var/www/html/documentation/index.php',
            'QUERY_STRING' =>''
        ];
        ob_start();
        $this->object->run();
        $response = ob_get_contents();
        ob_clean();

        $results = [];
        $results[] = new Result($response=="Test: <strong>me</strong>", "tested response");
        $results[] = new Result($this->attributes->getStartTime() && $this->attributes->getEndTime() && $this->attributes->getEndTime()>$this->attributes->getStartTime(), "tested event listeners");
        return $results;
    }
}
