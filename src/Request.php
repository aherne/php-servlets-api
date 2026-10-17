<?php

namespace Lucinda\STDOUT;

use Lucinda\STDOUT\Request\Client;
use Lucinda\STDOUT\Request\Method;
use Lucinda\STDOUT\Request\Protocol;
use Lucinda\STDOUT\Request\Server;
use Lucinda\STDOUT\Request\URI;
use Lucinda\STDOUT\Request\UploadedFiles;
use Lucinda\MVC\ConfigurationException;
use Lucinda\MVC\Facet;

/**
 * Captures an immutable snapshot of the current PHP HTTP request
 *
 * Construction reads $_SERVER, $_GET, $_POST, and $_FILES into typed client,
 * server, URI, method, protocol, header, parameter, and upload representations.
 * GET parameters are used for GET requests, POST parameters for POST requests,
 * and the parsed input stream for PUT and DELETE requests.
 *
 * The object does not mutate the superglobals after capturing them.
 *
 * @see Client
 * @see Server
 * @see URI
 * @see UploadedFiles
 */
final class Request implements Facet
{
    /**
     * Client endpoint information captured from $_SERVER
     */
    private Client $client;

    /**
     * Receiving server information captured from $_SERVER
     */
    private Server $server;

    /**
     * Parsed application-relative URI information
     */
    private URI $uRI;

    /**
     * Validated HTTP request method
     */
    private Method $method;

    /**
     * HTTP or HTTPS transport detected for the request
     */
    private Protocol $protocol;

    /**
     * @var array<string,string> Normalized request headers keyed by canonicalized name
     */
    private array $headers = array();

    /**
     * @var array<string,mixed> Parameters selected according to the request method
     */
    private array $parameters = array();

    /**
     * @var array<string,mixed> Uploaded File objects preserving the form field hierarchy
     */
    private array $uploadedFiles = array();

    /**
     * Captures and validates all supported request information
     *
     * @throws ConfigurationException If REQUEST_URI is unavailable
     * @throws UploadedFiles\Exception If PHP reports an upload error
     * @throws \TypeError If REQUEST_METHOD is not represented by Request\Method
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
     * Captures client endpoint information
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
     */
    private function setClient(array $server): void
    {
        $this->client = new Client($server);
    }

    /**
     * Gets the client endpoint that made the request
     *
     * @return Client Captured host name, address, and source port
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Captures receiving server information
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
     */
    private function setServer(array $server): void
    {
        $this->server = new Server($server);
    }

    /**
     * Gets the server endpoint that received the request
     *
     * @return Server Captured host, address, port, administrator, and software values
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * Parses the requested application-relative URI
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
     */
    private function setURI(array $server): void
    {
        $this->uRI = new URI($server);
    }

    /**
     * Gets parsed request URI information
     *
     * @return URI Context path, page, query string, and query parameters
     */
    public function getURI(): URI
    {
        return $this->uRI;
    }

    /**
     * Extracts and canonicalizes HTTP request headers
     *
     * Only $_SERVER keys beginning with `HTTP_` are included.
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
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
     * Gets all request headers or one named header
     *
     * Header lookup is case-sensitive against the canonicalized stored names.
     *
     * @param string $name Canonicalized header name, or an empty string for all headers
     * @return string|array<string,string>|null All headers, the selected value, or null when absent
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
     * Selects request parameters according to the HTTP method
     *
     * @param array<string,mixed> $get Snapshot of $_GET
     * @param array<string,mixed> $post Snapshot of $_POST
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
     * Gets all request parameters or one named parameter
     *
     * @param string $name Parameter name, or an empty string for the complete map
     * @return mixed Complete parameter map, selected value, or null when absent
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
     * Converts the $_FILES snapshot into typed uploaded-file values
     *
     * Each uploaded leaf becomes a File while nested form field structure is
     * retained in the resulting array.
     *
     * @param array<string,mixed> $files Snapshot of $_FILES
     * @throws UploadedFiles\Exception If PHP reports an upload error
     */
    private function setUploadedFiles(array $files): void
    {
        $files = new UploadedFiles($files);
        $this->uploadedFiles = $files->toArray();
    }

    /**
     * Gets all uploaded files or one named upload field
     *
     * @param string $name Upload field name, or an empty string for the complete tree
     * @return mixed Complete upload tree, selected File or subtree, or null when absent
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
     * Parses the HTTP request method
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
     * @throws \TypeError If REQUEST_METHOD is not represented by Request\Method
     */
    private function setMethod(array $server): void
    {
        $this->method = Method::tryFrom($server["REQUEST_METHOD"]);
    }

    /**
     * Gets the HTTP request method
     *
     * @return Method Parsed request method
     */
    public function getMethod(): Method
    {
        return $this->method;
    }

    /**
     * Detects whether the request used HTTP or HTTPS
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
     */
    private function setProtocol(array $server): void
    {
        $this->protocol = Protocol::tryFrom(!empty($server['HTTPS']) ? "https" : "http");
    }

    /**
     * Gets the request protocol
     *
     * @return Protocol HTTP or HTTPS transport
     */
    public function getProtocol(): Protocol
    {
        return $this->protocol;
    }

    /**
     * Reads the remaining raw request body
     *
     * @return string Current contents of php://input
     */
    public function getInputStream(): string
    {
        return file_get_contents("php://input");
    }
}
