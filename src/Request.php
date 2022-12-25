<?php

namespace Lucinda\STDOUT;

use Lucinda\STDOUT\Request\Client;
use Lucinda\STDOUT\Request\Method;
use Lucinda\STDOUT\Request\Protocol;
use Lucinda\STDOUT\Request\Server;
use Lucinda\STDOUT\Request\URI;
use Lucinda\STDOUT\Request\UploadedFiles;
use Lucinda\STDOUT\Request\UploadedFiles\File;
use Lucinda\MVC\ConfigurationException;

/**
 * Detects information about request from $_SERVER, $_GET, $_POST, $_FILES. Once detected, parameters are immutable.
 */
class Request
{
    private Client $client;
    private Server $server;
    private URI $uRI;
    private Method $method;
    private Protocol $protocol;
    /**
     * @var array<string, string>
     */
    private array $headers = array();
    /**
     * @var array<string, mixed>
     */
    private array $parameters = array();
    /**
     * @var array<string, mixed>
     */
    private array $uploadedFiles = array();

    /**
     * Detects all aspects of a request.
     *
     * @throws ConfigurationException|UploadedFiles\Exception
     */
    public function __construct()
    {
        $server = $_SERVER;
        $get = $_GET;
        $post = $_POST;
        $files = $_FILES;

        if (!isset($server["REQUEST_URI"])) {
            throw new ConfigurationException("API requires overriding paths!");
        }

        $this->setClient($server);
        $this->setServer($server);
        $this->setMethod($server);
        $this->setProtocol($server);
        $this->setURI($server);
        $this->setHeaders($server);
        $this->setParameters($get, $post);
        $this->setUploadedFiles($files);
    }

    /**
     * Sets information about client that made the request.
     *
     * @param array<string,string> $server
     */
    private function setClient(array $server): void
    {
        $this->client = new Client($server);
    }

    /**
     * Gets information about client that made the request.
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Sets information about server that received the request.
     *
     * @param array<string,string> $server
     */
    private function setServer(array $server): void
    {
        $this->server = new Server($server);
    }

    /**
     * Gets information about server that received the request.
     *
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * Sets information about URI client requested (host, page, url, etc).
     *
     * @param array<string,string> $server
     */
    private function setURI(array $server): void
    {
        $this->uRI = new URI($server);
    }

    /**
     * Gets information about URI client requested (host, page, url, etc).
     *
     * @return URI
     */
    public function getURI(): URI
    {
        return $this->uRI;
    }

    /**
     * Sets headers sent by client.
     *
     * @param array<string,string> $server
     */
    private function setHeaders(array $server): void
    {
        foreach ($server as $name => $value) {
            if (str_starts_with($name, "HTTP_")) {
                $this->headers[str_replace(
                    ' ',
                    '-',
                    ucwords(strtolower(str_replace('_', ' ', substr($name, 5))))
                )
                ] = $value;
            }
        }
    }

    /**
     * Gets request headers detected by optional name
     *
     * @param  string $name
     * @return string|array<string,string>|null
     */
    public function headers(string $name=""): string|array|null
    {
        if (!$name) {
            return $this->headers;
        } else {
            return ($this->headers[$name] ?? null);
        }
    }

    /**
     * Sets parameters sent by client in accordance to HTTP request method.
     *
     * @param array<string,mixed> $get
     * @param array<string,mixed> $post
     */
    private function setParameters(array $get, array $post): void
    {
        switch ($this->method) {
            case Method::GET:
                $this->parameters = $get;
                break;
            case Method::POST:
                $this->parameters = $post;
                break;
            case Method::PUT:
            case Method::DELETE:
                $postVars = array();
                parse_str(file_get_contents("php://input"), $postVars);
                $this->parameters = $postVars;
                break;
            default:
                $this->parameters = array();
                break;
        }
    }

    /**
     * Gets request parameters detected by optional name
     *
     * @param  string $name
     * @return mixed
     */
    public function parameters(string $name=""): mixed
    {
        if (!$name) {
            return $this->parameters;
        } else {
            return ($this->parameters[$name] ?? null);
        }
    }

    /**
     * Sets files uploaded by client via form based on PHP superglobal $_FILES with two structural changes:
     * - uploaded file attributes (name, type, tmp_name, etc) are encapsulated into an UploadedFile instance
     * - array structure information is saved to follows exactly structure set in file input @ form.
     *
     * @param  array<string,mixed> $files
     * @throws UploadedFiles\Exception
     */
    private function setUploadedFiles(array $files): void
    {
        $files = new UploadedFiles($files);
        $this->uploadedFiles = $files->toArray();
    }

    /**
     * Gets uploaded files detected by optional request parameter name
     *
     * @param  string $name
     * @return mixed
     */
    public function uploadedFiles(string $name=""): mixed
    {
        if (!$name) {
            return $this->uploadedFiles;
        } else {
            return ($this->uploadedFiles[$name] ?? null);
        }
    }

    /**
     * Sets HTTP request method in which URI was requested.
     *
     * @param array<string,string> $server
     */
    private function setMethod(array $server): void
    {
        $this->method = Method::tryFrom($server["REQUEST_METHOD"]);
    }

    /**
     * Gets HTTP request method in which URI was requested.
     *
     * @example GET
     * @return  Method
     */
    public function getMethod(): Method
    {
        return $this->method;
    }

    /**
     * Sets protocol for which URI was requested.
     *
     * @param array<string,string> $server
     */
    private function setProtocol(array $server): void
    {
        $this->protocol = Protocol::tryFrom(!empty($server['HTTPS']) ? "https" : "http");
    }

    /**
     * Gets protocol for which URI was requested.
     *
     * @example https
     * @return  Protocol
     */
    public function getProtocol(): Protocol
    {
        return $this->protocol;
    }

    /**
     * Gets input stream contents.
     *
     * @return string
     */
    public function getInputStream(): string
    {
        return file_get_contents("php://input");
    }
}
