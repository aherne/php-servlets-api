<?php
namespace Lucinda\STDOUT;

/**
 * Encapsulates transport layer that collects variables to be passed through API objects
 */
class Attributes
{
    private $eventsFolder;
    private $requestedPage;
    private $requestedResponseFormat;
    private $pathParameters=array();
    
    /**
     * Sets path to event listeners classes
     * 
     * @param string $eventsFolder
     */
    public function __construct($eventsFolder)
    {
        $this->eventsFolder = $eventsFolder;
    }
    
    /**
     * Gets path to event listeners classes
     * 
     * @return string
     */
    public function getEventsFolder()
    {
        return $this->eventsFolder;
    }
    
    /**
     * Sets requested page detected by matching original requested to XML directives
     *
     * @param string $page
     */
    public function setRequestedPage(string $page): void
    {
        $this->requestedPage = $page;
    }
    
    /**
     * Gets requested page detected by matching original requested to XML directives
     *
     * @example /asd/def
     * @return string
     */
    public function getRequestedPage(): string
    {
        return $this->requestedPage;
    }
    
    /**
     * Gets path parameters detected from requested page, optionally by parameter name
     *
     * @param string[string] $parameters
     */
    public function setPathParameters(array $parameters): void
    {
        $this->pathParameters = $parameters;
    }
        
    /**
     * Gets path parameters detected from requested page, optionally by parameter name
     *
     * @param string $name
     * @return string|array|null
     */
    public function getPathParameters(string $name="")
    {
        if (!$name) {
            return $this->pathParameters;
        } else {
            return (isset($this->pathParameters[$name])?$this->pathParameters[$name]:null);
        }
    }
    
    /**
     * Gets requested response format detected by matching original to XML directives
     *
     * @param string $format
     */
    public function setRequestedResponseFormat(string $format): void
    {
        $this->requestedResponseFormat = $format;
    }
    
    /**
     * Gets requested response format detected by matching original requested to XML directives
     *
     * @example html
     * @return string
     */
    public function getRequestedResponseFormat(): string
    {
        return $this->requestedResponseFormat;
    }
}
