<?php
namespace Lucinda\STDOUT;

use Lucinda\STDOUT\Response\Status;
use Lucinda\STDOUT\Response\View;

/**
 * Compiles information about response
 */
class Response
{
    private $status;
    private $headers = array();
    private $body;
    private $view;

    /**
     * Constructs an empty response based on content type
     *
     * @param string $contentType Value of content type header that will be sent in response
     * @param string $templateFile Value of view template file that will form the basis of response
     */
    public function __construct(string $contentType, string $templateFile)
    {
        $this->headers["Content-Type"] = $contentType;
        $this->view = new View($templateFile);
    }

    /**
     * Sets HTTP response status by its numeric code.
     *
     * @param integer $code
     * @throws Exception If status code is invalid.
     */
    public function setStatus(int $code): void
    {
        $this->status = new Status($code);
    }

    /**
     * Gets HTTP response status info.
     *
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }
    
    /**
     * Gets a pointer to View
     *
     * @return View
     */
    public function view(): View
    {
        return $this->view;
    }
    
    /**
     * Gets or sets response headers will send back to user.
     *
     * @param string $key
     * @param string $value
     * @return string|array|null
     */
    public function headers(string $key="", string $value=null)
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
    public function setBody(string $body): void
    {
        $this->body = $body;
    }
    
    /**
     * Gets response body
     *
     * @return string
     */
    public function getBody(): string
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
    public static function redirect(string $location, bool $permanent=true, bool $preventCaching=false): void
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
    public function commit(): void
    {
        // sends headers
        if (!headers_sent()) {
            if ($this->status) {
                header("HTTP/1.1 ".$this->status->getId()." ".$this->status->getDescription());
            }
            
            foreach ($this->headers as $name=>$value) {
                header($name.": ".$value);
            }
        }
        
        // displays body
        echo $this->body;
    }
}
