<?php
namespace Lucinda\STDOUT;

use Lucinda\STDOUT\Request\Client;
use Lucinda\STDOUT\Request\Server;
use Lucinda\STDOUT\Request\URI;
use Lucinda\STDOUT\Request\UploadedFiles;

/**
 * Detects information about request from $_SERVER, $_GET, $_POST, $_FILES. Once detected, parameters are immutable.
 */
class Request
{
    private $client;
    private $server;
    private $uRI;
    private $method;
    private $protocol;    
    private $headers = array();
    private $parameters = array();
    private $uploadedFiles = array();
    
    /**
     * Detects all aspects of a request.
     * 
     * @throws Exception
     */
    public function __construct()
    {
        if (!isset($_SERVER["REQUEST_URI"])) {
            throw new Exception("ServletsAPI requires overriding paths!");
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
    private function setClient()
    {
        $this->client = new Client();
    }

    /**
     * Gets information about client that made the request.
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Sets information about server that received the request.
     */
    private function setServer()
    {
        $this->server = new Server();
    }

    /**
     * Gets information about server that received the request.
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Sets information about URI client requested (host, page, url, etc).
     */
    private function setURI()
    {
        $this->uRI = new URI();
    }

    /**
     * Gets information about URI client requested (host, page, url, etc).
     * @return URI
     */
    public function getURI()
    {
        return $this->uRI;
    }
    
    /**
     * Sets headers sent by client.
     */
    private function setHeaders()
    {
        foreach ($_SERVER as $name => $value) {
            if (strpos($name, "HTTP_") === 0) {
                $this->headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
    }
    
    /**
     * Gets request headers detected by optional name
     *
     * @param string $name
     * @return string[string]|NULL|string
     */
    public function headers($name="")
    {
        if (!$name) {
            return $this->headers;
        } else {
            return (isset($this->headers[$name])?$this->headers[$name]:null);
        }
    }

    /**
     * Sets parameters sent by client in accordance to HTTP request method.
     */
    private function setParameters()
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
     * @return mixed
     */
    public function parameters($name="")
    {
        if (!$name) {
            return $this->parameters;
        } else {
            return (isset($this->parameters[$name])?$this->parameters[$name]:null);
        }
    }
    
    /**
     * Sets files uploaded by client via form based on PHP superglobal $_FILES with two structural changes:
     * - uploaded file attributes (name, type, tmp_name, etc) are encapsulated into an UploadedFile instance
     * - array structure information is saved to follows exactly structure set in file input @ form.
     */
    private function setUploadedFiles()
    {
        $files = new UploadedFiles();
        $this->uploadedFiles = $files->toArray();
    }
    
    /**
     * Gets uploaded files detected by optional request parameter name
     *
     * @param string $name
     * @return mixed
     */
    public function uploadedFiles($name="")
    {
        if (!$name) {
            return $this->uploadedFiles;
        } else {
            return (isset($this->uploadedFiles[$name])?$this->uploadedFiles[$name]:null);
        }
    }

    /**
     * Sets HTTP request method in which URI was requested.
     *
     * @return void
     */
    private function setMethod()
    {
        $this->method=$_SERVER["REQUEST_METHOD"];
    }
    
    /**
     * Gets HTTP request method in which URI was requested.
     *
     * @example GET
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * Sets protocol for which URI was requested.
     */
    private function setProtocol()
    {
        $this->protocol = (!empty($_SERVER['HTTPS'])?"https":"http");
    }
    
    /**
     * Gets protocol for which URI was requested.
     *
     * @example https
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }
    
    /**
     * Gets input stream contents.
     *
     * @return string
     */
    public function getInputStream()
    {
        return file_get_contents("php://input");
    }
}
