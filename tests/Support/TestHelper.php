<?php

namespace Test\Lucinda\STDOUT\Support;

use Lucinda\MVC\EventScheduler;
use Lucinda\MVC\XmlReader\Element;
use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\Request;

final class TestHelper
{
    public static function fixturesPath(string $path = ""): string
    {
        $root = dirname(__DIR__) . DIRECTORY_SEPARATOR . "fixtures";
        return $path ? $root . DIRECTORY_SEPARATOR . $path : $root;
    }

    public static function rootXmlPath(): string
    {
        return self::fixturesPath("root.xml");
    }

    public static function application(): Application
    {
        return new Application(self::rootXmlPath());
    }

    /**
     * @param array<string, string> $overrides
     * @return array<string, string>
     */
    public static function requestServer(array $overrides = []): array
    {
        return array_replace([
            "HTTP_HOST" => "www.test.local",
            "HTTP_USER_AGENT" => "Lucinda Test Agent",
            "HTTP_ACCEPT" => "text/html,application/json",
            "HTTP_ACCEPT_LANGUAGE" => "en-US",
            "HTTP_ACCEPT_ENCODING" => "gzip",
            "HTTP_CONNECTION" => "keep-alive",
            "HTTP_X_REQUESTED_WITH" => "XMLHttpRequest",
            "SERVER_ADMIN" => "admin@test.local",
            "SERVER_SOFTWARE" => "PHP Built-In Server",
            "SERVER_NAME" => "www.documentation.local",
            "SERVER_ADDR" => "127.0.0.1",
            "SERVER_PORT" => "8080",
            "REMOTE_HOST" => "client.local",
            "REMOTE_ADDR" => "127.0.0.2",
            "REMOTE_PORT" => "59300",
            "REQUEST_URI" => "/users",
            "REQUEST_METHOD" => "GET",
            "DOCUMENT_ROOT" => "/var/www/html/project",
            "SCRIPT_FILENAME" => "/var/www/html/project/index.php",
            "QUERY_STRING" => "",
        ], $overrides);
    }

    /**
     * @param array<string, string> $server
     * @param array<string, mixed> $get
     * @param array<string, mixed> $post
     * @param array<string, mixed> $files
     */
    public static function setRequestGlobals(
        array $server,
        array $get = [],
        array $post = [],
        array $files = []
    ): void {
        $_SERVER = $server;
        $_GET = $get;
        $_POST = $post;
        $_FILES = $files;
    }

    /**
     * @param array<string, string> $server
     * @param array<string, mixed> $get
     * @param array<string, mixed> $post
     * @param array<string, mixed> $files
     */
    public static function request(
        array $server,
        array $get = [],
        array $post = [],
        array $files = []
    ): Request {
        self::setRequestGlobals($server, $get, $post, $files);
        return new Request();
    }

    public static function element(string $xml): Element
    {
        return new Element(simplexml_load_string($xml));
    }

    public static function tempFile(string $name, string $content = "fixture"): string
    {
        $path = self::fixturesPath($name);
        file_put_contents($path, $content);
        return $path;
    }

    public static function logPath(): string
    {
        return self::fixturesPath("front-controller.log");
    }

    public static function resetFrontControllerLog(): void
    {
        file_put_contents(self::logPath(), "");
    }

    public static function logEvent(string $message): void
    {
        file_put_contents(self::logPath(), $message . PHP_EOL, FILE_APPEND);
    }

    public static function eventScheduler(): EventScheduler
    {
        return new EventScheduler();
    }

    public static function resetSessionState(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        $_SESSION = [];
        $_COOKIE = [];
        session_id("");
        session_name("PHPSESSID");
    }
}
