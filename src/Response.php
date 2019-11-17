<?php
namespace Lucinda\MVC\STDOUT;

require("response/ResponseStream.php");
require("response/ResponseStatus.php");

/**
 * Compiles information about response
 */
class Response
{
    private $headers = array();
    private $attributes = array();
    private $status;
    private $viewPath;
    private $outputStream;
    private $isDisabled;

    /**
     * Constructs an empty response based on content type
     *
     * @param string $contentType Value of content type header that will be sent in response
     */
    public function __construct($contentType)
    {
        $this->outputStream	= new ResponseStream();
        $this->headers["Content-Type"] = $contentType;
    }

    /**
     * Gets response stream to work on.
     *
     * @return ResponseStream
     */
    public function getOutputStream()
    {
        return $this->outputStream;
    }

    /**
     * Redirects to a new location.
     *
     * @param string $location
     * @param boolean $permanent
     * @param boolean $preventCaching
     */
    public function redirect($location, $permanent=true, $preventCaching=false)
    {
        if ($preventCaching) {
            header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
            header("Pragma: no-cache");
            header("Expires: 0");
        }
        header('Location: '.$location, true, $permanent?301:302);
        exit();
    }

    /**
     * Forwards response to a file (aka "view")
     *
     * @param string $viewPath
     */
    public function setView($viewPath)
    {
        $this->viewPath = $viewPath;
    }

    /**
     * Gets view's absolute path.
     *
     * @return string
     */
    public function getView()
    {
        return $this->viewPath;
    }

    /**
     * Sets HTTP response status by its numeric code.
     *
     * @param int $code
     * @throws ServletException If status code is invalid.
     */
    public function setStatus($code)
    {
        $this->status = new ResponseStatus($code);
    }

    /**
     * Gets HTTP response status info.
     *
     * @return ResponseStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Disables response. A disabled response will output nothing.
     */
    public function disable()
    {
        $this->isDisabled = true;
    }

    /**
     * Checks if response is disabled.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->isDisabled;
    }

    /**
     * Commits response to client.
     */
    public function commit()
    {
        if (!headers_sent() && $this->status) {
            header("HTTP/1.1 ".$this->status->getId()." ".$this->status->getDescription());
        }

        if (!$this->isDisabled) {
            // load headers
            $headers = $this->headers;
            if (sizeof($headers)>0) {
                foreach ($headers as $name=>$value) {
                    header($name.": ".$value);
                }
            }

            // show output
            echo $this->outputStream->get();
        }
    }
    
    /**
     * Gets or sets response headers will send back to user.
     *
     * @param string $key
     * @param string $value
     * @return string[string]|NULL|string
     */
    public function headers($key="", $value=null)
    {
        if (!$key) {
            return $this->headers;
        } elseif ($value===null) {
            return (isset($this->headers[$key])?$this->headers[$key]:null);
        } else {
            $this->headers[$key] = $value;
        }
    }
    
    /**
     * Gets or sets data that will be sent to views.
     *
     * @param string $key
     * @param string $value
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
}
