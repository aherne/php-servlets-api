<?php
namespace Lucinda\STDOUT;

use Lucinda\STDOUT\Response\Status;

/**
 * Compiles information about response
 */
class Response
{
    private $status;
    private $headers = array();
    private $body;

    /**
     * Constructs an empty response based on content type
     *
     * @param string $contentType Value of content type header that will be sent in response
     */
    public function __construct($contentType)
    {
        $this->headers["Content-Type"] = $contentType;
    }

    /**
     * Sets HTTP response status by its numeric code.
     *
     * @param int $code
     * @throws Exception If status code is invalid.
     */
    public function setStatus($code)
    {
        $this->status = new Status($code);
    }

    /**
     * Gets HTTP response status info.
     *
     * @return Status
     */
    public function getStatus()
    {
        return $this->status;
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
     * Sets response body
     * 
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
    
    /**
     * Gets response body
     * 
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * Redirects to a new location.
     *
     * @param string $location
     * @param boolean $permanent
     * @param boolean $preventCaching
     */
    public static function redirect($location, $permanent=true, $preventCaching=false)
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
     * Commits response to client.
     */
    public function commit()
    {
        if (!headers_sent() && $this->status) {
            header("HTTP/1.1 ".$this->status->getId()." ".$this->status->getDescription());
        }
        
        // load headers
        $headers = $this->headers;
        if (sizeof($headers)>0) {
            foreach ($headers as $name=>$value) {
                header($name.": ".$value);
            }
        }
        
        // show output
        echo $this->body;
    }
}
