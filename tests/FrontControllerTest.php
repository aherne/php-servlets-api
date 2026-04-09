<?php
namespace Test\Lucinda\STDOUT;

use Lucinda\MVC\EventType;
use Lucinda\UnitTest\Validator\Files;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\EventListeners\EndTracker;
use Test\Lucinda\STDOUT\Support\EventListeners\StartTracker;
use Test\Lucinda\STDOUT\Support\TestHelper;
use Test\Lucinda\STDOUT\Support\Transformers\BodySuffixTransformer;

class FrontControllerTest
{
    private \Lucinda\STDOUT\FrontController $object;

    public function __construct()
    {
        $scheduler = TestHelper::eventScheduler();
        $scheduler->add(EventType::START, StartTracker::class);
        $scheduler->add(EventType::RESPONSE, BodySuffixTransformer::class);
        $scheduler->add(EventType::END, EndTracker::class);
        $this->object = new \Lucinda\STDOUT\FrontController(TestHelper::rootXmlPath(), $scheduler);
    }

    public function run()
    {
        TestHelper::resetFrontControllerLog();
        TestHelper::setRequestGlobals(
            TestHelper::requestServer([
                "REQUEST_URI" => "/users",
                "QUERY_STRING" => "",
            ])
        );
        $_COOKIE = [];
        TestHelper::resetSessionState();

        ob_start();
        $this->object->run();
        $response = (string) ob_get_clean();

        return [
            (new Strings($response))->assertEquals("Hello from controller!"),
            (new Files(TestHelper::logPath()))->assertContains("start"),
            (new Files(TestHelper::logPath()))->assertContains("end"),
        ];
    }
}
