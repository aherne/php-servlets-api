<?php
namespace Lucinda\MVC\STDOUT;

require("request/RequestClient.php");
require("request/RequestServer.php");
require("request/RequestURI.php");
require("request/UploadedFileTree.php");
require("request/Session.php");
require("request/Cookie.php");
require("request/RequestValidator.php");

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
    
    private $cookie;
    private $session;
    private $headers = array();
    private $parameters = array();
    private $uploadedFiles;
    
    private $validator;
    private $attributes = array();
    
    /**
     * Detects all aspects of a request.
     */
    public function __construct()
    {
        $this->setClient();
        $this->setServer();
        $this->setMethod();
        $this->setProtocol();
        $this->setURI();
        // set params
        $this->setCookie();
        $this->setSession();
        $this->setHeaders();
        $this->setParameters();
        $this->setUploadedFiles();
    }
    
    /**
     * Sets information about client that made the request.
     */
    private function setClient()
    {
        $this->client = new RequestClient();
    }

    /**
     * Gets information about client that made the request.
     * @return RequestClient
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
        $this->server = new RequestServer();
    }

    /**
     * Gets information about server that received the request.
     * @return RequestServer
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
        $this->uRI = new RequestURI();
    }

    /**
     * Gets information about URI client requested (host, page, url, etc).
     * @return RequestURI
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
     * Sets files uploaded by client via form based on PHP superglobal $_FILES with two structural changes:
     * - uploaded file attributes (name, type, tmp_name, etc) are encapsulated into an UploadedFile instance
     * - array structure information is saved to follows exactly structure set in file input @ form.
     */
    private function setUploadedFiles()
    {
        $files = new UploadedFileTree();
        $this->uploadedFiles = $files->toArray();
    }
    
    /**
     * Gets files uploaded as tree of UploadedFile objects following request structure.
     *
     * @return array
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }
    
    /**
     * Encapsulates parameters received as $_COOKIE
     */
    private function setCookie()
    {
        $this->cookie = new Cookie();
    }

    /**
     * Gets pointer to cookie (to be used in gettin/setting cookie params)
     *
     * @return Cookie
     */
    public function getCookie()
    {
        return $this->cookie;
    }

    /**
     * Encapsulates parameters received as $_SESSION
     */
    private function setSession()
    {
        $this->session = new Session();
    }


    /**
     * Gets pointer to cookie (to be used in gettin/setting session params)
     *
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
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
    
    /**
     * Sets class able to process request and extract information about:
     * - actual page requested
     * - content type requested
     * - path parameters present in request
     *
     * @param RequestValidator $validator
     */
    public function setValidator(RequestValidator $validator)
    {
        $this->validator = $validator;
    }
    
    /**
     * Gets class to extract information about:
     * - actual page requested
     * - content type requested
     * - path parameters present in request
     *
     * @return RequestValidator
     */
    public function getValidator()
    {
        return $this->validator;
    }
    
    /**
     * Gets or sets request attributes
     *
     * @param string $key
     * @param mixed $value
     * @return mixed[string]|NULL|mixed
     */
    public function attributes($key="", $value=null)
    {
        if (!$key) {
            return $this->attributes;
        } elseif ($value===null) {
            return (isset($this->attributes[$key])?$this->attributes[$key]:null);
        } else {
            $this->attributes[$key] = $value;
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
     * Gets request parameters detected by optional name
     *
     * @param string $name
     * @return mixed[string]|NULL|mixed
     */
    public function parameters($name="")
    {
        if (!$name) {
            return $this->parameters;
        } else {
            return (isset($this->parameters[$name])?$this->parameters[$name]:null);
        }
    }
}
