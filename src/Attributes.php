<?php
namespace Lucinda\STDOUT;

/**
 * Encapsulates transport layer that collects variables to be passed through API objects
 */
class Attributes
{
    private string $requestedPage;
    private string $requestedResponseFormat;
    private array $pathParameters = [];
    private array $validParameters = [];
    
    /**
     * Sets requested page detected by matching original requested to XML directives
     *
     * @param string $page
     */
    public function setValidPage(string $page): void
    {
        $this->requestedPage = $page;
    }
    
    /**
     * Gets requested page detected by matching original requested to XML directives
     *
     * @example /asd/def
     * @return string
     */
    public function getValidPage(): string
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
    public function getPathParameters(string $name=""): string|array|null
    {
        if (!$name) {
            return $this->pathParameters;
        } else {
            return ($this->pathParameters[$name] ?? null);
        }
    }
    
    /**
     * Sets route/request parameter validation results for requested page, optionally by parameter name
     *
     * @param string[string] $parameters
     */
    public function setValidParameters(array $parameters): void
    {
        $this->validParameters = $parameters;
    }
    
    /**
     * Gets route/request parameter validation results for requested page, optionally by parameter name
     *
     * @param string $name
     * @return string|array|null
     */
    public function getValidParameters(string $name=""): string|array|null
    {
        if (!$name) {
            return $this->validParameters;
        } else {
            return ($this->validParameters[$name] ?? null);
        }
    }
    
    /**
     * Gets requested response format detected by matching original to XML directives
     *
     * @param string $format
     */
    public function setValidFormat(string $format): void
    {
        $this->requestedResponseFormat = $format;
    }
    
    /**
     * Gets requested response format detected by matching original requested to XML directives
     *
     * @example html
     * @return string
     */
    public function getValidFormat(): string
    {
        return $this->requestedResponseFormat;
    }
}
