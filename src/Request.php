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
    private array $headers = array();
    private array $parameters = array();
    private array $uploadedFiles = array();

    /**
     * Detects all aspects of a request.
     *
     * @throws ConfigurationException
     */
    public function __construct()
    {
        if (!isset($_SERVER["REQUEST_URI"])) {
            throw new ConfigurationException("API requires overriding paths!");
        }

        $this->setClient();
        $this->setServer();
        $this->setMethod();
        $this->setProtocol();
        $this->setURI();
        $this->setHeaders();
        $this->setParameters();
        $this->setUploadedFiles();
    }

    /**
     * Sets information about client that made the request.
     */
    private function setClient(): void
    {
        $this->client = new Client();
    }

    /**
     * Gets information about client that made the request.
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Sets information about server that received the request.
     */
    private function setServer(): void
    {
        $this->server = new Server();
    }

    /**
     * Gets information about server that received the request.
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * Sets information about URI client requested (host, page, url, etc).
     */
    private function setURI(): void
    {
        $this->uRI = new URI();
    }

    /**
     * Gets information about URI client requested (host, page, url, etc).
     * @return URI
     */
    public function getURI(): URI
    {
        return $this->uRI;
    }

    /**
     * Sets headers sent by client.
     */
    private function setHeaders(): void
    {
        foreach ($_SERVER as $name => $value) {
            if (str_starts_with($name, "HTTP_")) {
                $this->headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
    }

    /**
     * Gets request headers detected by optional name
     *
     * @param string $name
     * @return string|array|null
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
     */
    private function setParameters(): void
    {
        switch ($_SERVER["REQUEST_METHOD"]) {
            case "GET":
                $this->parameters = $_GET;
                break;
            case "POST":
                $this->parameters = $_POST;
                break;
            case "PUT":
            case "DELETE":
                $post_vars = array();
                parse_str(file_get_contents("php://input"), $post_vars);
                $this->parameters = $post_vars;
                break;
            default:
                $this->parameters = array();
                break;
        }
    }

    /**
     * Gets request parameters detected by optional name
     *
     * @param string $name
     * @return string|array|null
     */
    public function parameters(string $name=""): array|string|null
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
     */
    private function setUploadedFiles(): void
    {
        $files = new UploadedFiles();
        $this->uploadedFiles = $files->toArray();
    }

    /**
     * Gets uploaded files detected by optional request parameter name
     *
     * @param string $name
     * @return File|array|null
     */
    public function uploadedFiles(string $name=""): array|File|null
    {
        if (!$name) {
            return $this->uploadedFiles;
        } else {
            return ($this->uploadedFiles[$name] ?? null);
        }
    }

    /**
     * Sets HTTP request method in which URI was requested.
     */
    private function setMethod(): void
    {
        $this->method = Method::tryFrom($_SERVER["REQUEST_METHOD"]);
    }

    /**
     * Gets HTTP request method in which URI was requested.
     *
     * @example GET
     * @return Method
     */
    public function getMethod(): Method
    {
        return $this->method;
    }

    /**
     * Sets protocol for which URI was requested.
     */
    private function setProtocol(): void
    {
        $this->protocol = Protocol::tryFrom(!empty($_SERVER['HTTPS'])?"https":"http");
    }

    /**
     * Gets protocol for which URI was requested.
     *
     * @example https
     * @return Protocol
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
