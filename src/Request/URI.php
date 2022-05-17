<?php

namespace Lucinda\STDOUT\Request;

/**
 * Encapsulates information about URI client requested.
 */
class URI
{
    private string $contextPath;
    private string $page;
    private string $queryString;
    /**
     * @var array<string,mixed>
     */
    private array $parameters = [];

    /**
     * Detects info based on values in $_SERVER superglobal
     *
     * @param array<string,string> $server
     */
    public function __construct(array $server)
    {
        $this->setContextPath($server);
        $this->setPage($server);
        $this->setQueryString($server);
        parse_str($this->queryString, $this->parameters);
    }

    /**
     * Sets context path from requested URL.
     *
     * @param array<string,string> $server
     */
    private function setContextPath(array $server): void
    {
        $this->contextPath = str_replace(array($server["DOCUMENT_ROOT"],"/index.php"), "", $server["SCRIPT_FILENAME"]);
    }

    /**
     * Gets context path from requested URL.
     *
     * @example "/servlets/" when url is "http://www.test.com/servlets/test.html?a=b&c=d"
     * @return string
     */
    public function getContextPath(): string
    {
        return $this->contextPath;
    }

    /**
     * Sets original page requested path based on REQUEST_URI
     *
     * @param array<string,string> $server
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
     * Gets original page requested path.
     *
     * @example "mypage.json" when url is "http://www.test.com/servlets/mypage.json?a=b&c=d"
     * @return string
     */
    public function getPage(): string
    {
        return $this->page;
    }

    /**
     * Sets query string part from requested URL
     *
     * @param array<string,string> $server
     */
    private function setQueryString(array $server): void
    {
        $this->queryString = $server["QUERY_STRING"];
    }

    /**
     * Gets query string part from requested URL.
     *
     * @example "a=b&c=d" when url is "http://www.test.com/servlets/mypage.json?a=b&c=d"
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->queryString;
    }

    /**
     * Gets query string parameters detected by optional name
     *
     * @param string $name
     * @return mixed
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
