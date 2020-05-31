<?php
namespace Test\Lucinda\STDOUT\EventListeners\Validators;

use Lucinda\STDOUT\Request;
use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\EventListeners\Validators\RouteValidator;
use Lucinda\UnitTest\Result;

class RouteValidatorTest
{
    private $object;
    
    public function __construct()
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
            'REQUEST_URI' => '/user/lucian',
            'REQUEST_METHOD' => 'GET',
            'DOCUMENT_ROOT' => '/var/www/html/documentation',
            'SCRIPT_FILENAME' => '/var/www/html/documentation/index.php',
            'QUERY_STRING' =>''
        ];
        $this->object = new RouteValidator(new Application(dirname(__DIR__, 2)."/configuration.xml"), new Request());
    }

    public function getUrl()
    {
        return new Result($this->object->getUrl()=="user/(name)");
    }
        

    public function getPathParameters()
    {
        return new Result($this->object->getPathParameters()==["name"=>"lucian"]);
    }
        

    public function getValidParameters()
    {
        return new Result($this->object->getValidParameters()==["name"=>1]);
    }
}
