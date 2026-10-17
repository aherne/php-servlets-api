<?php

namespace Lucinda\STDOUT\Request;

/**
 * Parses application-relative path and query information for a request
 *
 * The context path is derived from DOCUMENT_ROOT and SCRIPT_FILENAME. It is
 * removed from REQUEST_URI to produce the route page, while QUERY_STRING is
 * retained both as raw text and as parsed parameters.
 */
final class URI
{
    /**
     * Application path prefix containing the front controller
     */
    private string $contextPath;
    /**
     * Application-relative path without a leading slash or query string
     */
    private string $page;
    /**
     * Raw query string reported by the web server
     */
    private string $queryString;

    /**
     * @var array<string,mixed> Query parameters parsed with parse_str()
     */
    private array $parameters = [];

    /**
     * Parses URI values from a $_SERVER snapshot
     *
     * @param array<string,mixed> $server Server values containing path and query entries
     */
    public function __construct(array $server)
    {
        $this->setContextPath($server);
        $this->setPage($server);
        $this->setQueryString($server);
        parse_str($this->queryString, $this->parameters);
    }

    /**
     * Derives the application context path from filesystem server values
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
     */
    private function setContextPath(array $server): void
    {
        $this->contextPath = str_replace(array($server["DOCUMENT_ROOT"],"/index.php"), "", $server["SCRIPT_FILENAME"]);
    }

    /**
     * Gets the application context path
     *
     * @return string URL path prefix removed before route matching
     */
    public function getContextPath(): string
    {
        return $this->contextPath;
    }

    /**
     * Extracts the application-relative page from REQUEST_URI
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
     */
    private function setPage(array $server): void
    {
        $urlCombined = substr($server["REQUEST_URI"], strlen($this->contextPath));
        $questionPosition = strpos($urlCombined, "?");
        if ($questionPosition!==false) {
            $urlCombined = substr($urlCombined, 0, $questionPosition);
        }
        $this->page = (str_starts_with($urlCombined, "/") ? substr($urlCombined, 1) : $urlCombined); // remove trailing slash
    }

    /**
     * Gets the application-relative requested page
     *
     * @return string Path without context prefix, leading slash, or query string
     */
    public function getPage(): string
    {
        return $this->page;
    }

    /**
     * Captures the raw query string
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
     */
    private function setQueryString(array $server): void
    {
        $this->queryString = $server["QUERY_STRING"];
    }

    /**
     * Gets the raw query string
     *
     * @return string Value reported by QUERY_STRING
     */
    public function getQueryString(): string
    {
        return $this->queryString;
    }

    /**
     * Gets all query parameters or one named value
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
}
