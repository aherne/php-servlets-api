<?php
namespace Lucinda\MVC\STDOUT;

require_once("response/ResponseHeaders.php");
require_once("response/ResponseStream.php");
require_once("response/ResponseStatus.php");

/**
 * Compiles information about response
 */
final class Response extends AttributesFactory {
    private $headers;
    private $status;
    private $viewPath;
    private $outputStream;
    private $isDisabled;

    public function __construct($contentType) {
        $this->outputStream	= new ResponseStream();

        $this->headers = new ResponseHeaders();
        $this->headers->set("Content-Type", $contentType);
    }

    /**
     * Gets response stream to work on.
     *
     * @return ResponseStream
     */
    public function getOutputStream() {
        return $this->outputStream;
    }

    /**
     * Delegates to specialized object for response header operations.
     *
     * @return ResponseHeaders
     */
    public function headers() {
        return $this->headers;
    }

    /**
     * Redirects to a new location.
     *
     * @param string $location
     * @param boolean $permanent
     * @param boolean $preventCaching
     * @return void
     */
    public static function sendRedirect($location, $permanent=true, $preventCaching=false) {
        if($preventCaching) {
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
    public function setView($viewPath) {
        $this->viewPath = $viewPath;
    }

    /**
     * Gets view's absolute path.
     *
     * @return string
     */
    public function getView() {
        return $this->viewPath;
    }

    /**
     * Sets HTTP response status by its numeric code.
     *
     * @param int $code
     * @throws ServletException If status code is invalid.
     */
    public function setStatus($code) {
        $this->status = new ResponseStatus($code);
    }

    /**
     * Gets HTTP response status info.
     *
     * @return ResponseStatus
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Disables response. A disabled response will output nothing.
     */
    public function disable() {
        $this->isDisabled = true;
    }

    /**
     * Checks if response is disabled.
     *
     * @return boolean
     */
    public function isDisabled() {
        return $this->isDisabled;
    }

    /**
     * Commits response to client.
     */
    public function commit() {
        if(!headers_sent() && $this->status) {
            header("HTTP/1.1 ".$this->status->getId()." ".$this->status->getDescription());
        }

        if(!$this->isDisabled) {
            // load headers
            $headers = $this->headers->toArray();
            if(sizeof($headers)>0) {
                foreach($headers as $name=>$value) {
                    header($name.": ".$value);
                }
            }

            // show output
            echo $this->outputStream->get();
        }
    }
}
