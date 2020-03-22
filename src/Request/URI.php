<?php
namespace Lucinda\STDOUT\Request;

/**
 * Encapsulates information about URI client requested.
 */
class URI
{
    private $contextPath;
    private $page;
    private $queryString;
    private $parameters;

    /**
     * Detects info based on values in $_SERVER superglobal
     */
    public function __construct()
    {
        $this->setContextPath();
        $this->setPage();
        $this->setQueryString();
        $this->parameters = $_GET;
    }

    /**
     * Sets context path from requested URL.
     */
    private function setContextPath(): void
    {
        $this->contextPath = str_replace(array($_SERVER["DOCUMENT_ROOT"],"/index.php"), "", $_SERVER["SCRIPT_FILENAME"]);
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
     */
    private function setPage(): void
    {
        $urlCombined = substr($_SERVER["REQUEST_URI"], strlen($this->contextPath));
        $questionPosition = strpos($urlCombined, "?");
        if ($questionPosition!==false) {
            $urlCombined = substr($urlCombined, 0, $questionPosition);
        }
        $this->page = (strpos($urlCombined, "/")===0?substr($urlCombined, 1):$urlCombined); // remove trailing slash
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
     */
    private function setQueryString(): void
    {
        $this->queryString = $_SERVER["QUERY_STRING"];
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
     * @param string|integer $name
     * @return string|array|null
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
