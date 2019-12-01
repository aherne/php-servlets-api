<?php
namespace Lucinda\STDOUT;

use Lucinda\STDOUT\Application\Route;
use Lucinda\STDOUT\Application\Format;

/**
 * Compiles information about application.
 */
class Application
{
    /**
     * @var \SimpleXMLElement
     */
    private $simpleXMLElement;
    private $defaultPage;
    private $defaultFormat;
    private $controllerPath;
    private $viewResolversPath;
    private $viewsPath;
    private $publicPath;
    private $autoRouting;
    private $version;
    private $routes = array();
    private $formats = array();
    
    /**
     * Populates attributes based on an XML file
     *
     * @param string $xmlFilePath XML file url
     * @throws Exception If xml file wasn't found
     * @throws XMLException If xml content has failed validation.
     */
    public function __construct(string $xmlFilePath): void
    {
        if (!file_exists($xmlFilePath)) {
            throw new Exception("XML file not found: ".$xmlFilePath);
        }
        $this->simpleXMLElement = simplexml_load_file($xmlFilePath);
        
        $this->setApplicationInfo();
        
        if (!$this->autoRouting) {
            $this->setRoutes();
        }
        
        $this->setFormats();
    }
    
    /**
     * Sets basic application info based on contents of "application" XML tag
     * @throws XMLException If xml content has failed validation.
     */
    private function setApplicationInfo(): void
    {
        $xml = $this->getTag("application");
        if (empty($xml)) {
            throw new XMLException("Tag is mandatory: application");
        }
        $this->defaultPage = (string) $xml["default_page"];
        if (!$this->defaultPage) {
            throw new XMLException("Attribute 'default_page' is mandatory for 'application' tag");
        }
        $this->defaultFormat = (string) $xml["default_format"];
        if (!$this->defaultFormat) {
            throw new XMLException("Attribute 'default_format' is mandatory for 'application' tag");
        }
        $this->controllerPath = (string) $xml->paths->controllers;
        $this->viewResolversPath = (string) $xml->paths->resolvers;
        $this->viewsPath = (string) $xml->paths->views;
        $this->publicPath = (string) $xml->paths->public;
        $this->autoRouting = (int) $xml["auto_routing"];
        $this->version = (string) $xml["version"];
    }
    
    /**
     * Sets user-defined routes that map to possible requested pages based on contents of "routes" XML tag
     * NOTICE: Only executed when auto_routing=0
     * @throws XMLException If xml content has failed validation.
     */
    private function setRoutes(): void
    {
        $tmp = (array) $this->getTag("routes");
        if (empty($tmp["route"])) {
            throw new XMLException("Tag 'routes' missing 'route' subtags");
        }
        $list = (is_array($tmp["route"])?$tmp["route"]:[$tmp["route"]]);
        foreach ($list as $info) {
            if (empty($info['url'])) {
                throw new XMLException("Attribute 'url' is mandatory for 'route' tag");
            }
            $url = (string) $info['url'];
            $this->routes[$url] = new Route($url, (string) $info['controller'], (string) $info['view'], (string) $info['format']);
        }
        if (empty($this->routes)) {
            throw new XMLException("Tag 'routes' is mandatory");
        }
    }
    
    /**
     * Sets user-defined file response formats that will be used by application based on contents of "formats" XML tag
     * @throws XMLException If xml content has failed validation.
     */
    private function setFormats(): void
    {
        $tmp = (array) $this->getTag("formats");
        if (empty($tmp["format"])) {
            throw new XMLException("Tag 'format' child of 'formats' tag is mandatory");
        }
        $list = (is_array($tmp["format"])?$tmp["format"]:[$tmp["format"]]);
        foreach ($list as $info) {
            if (empty($info['name'])) {
                throw new XMLException("Attribute 'name' is mandatory for 'format' tag");
            }
            if (empty($info['content_type'])) {
                throw new XMLException("Attribute 'content_type' is mandatory for 'format' tag");
            }
            $name = (string) $info['name'];
            $this->formats[$name] = new Format(
                $name,
                (string) $info['content_type'],
                (isset($info['charset'])?(string) $info['charset']:""),
                (isset($info['class'])?(string) $info['class']:"")
            );
        }
        if (empty($this->formats)) {
            throw new XMLException("Tag 'formats' is mandatory");
        }
    }
    
    /**
     * Gets default landing page.
     *
     * @return string
     */
    public function getDefaultPage(): string
    {
        return $this->defaultPage;
    }
    
    /**
     * Gets default response format name (eg: html or json)
     *
     * @return string
     */
    public function getDefaultFormat(): string
    {
        return $this->defaultFormat;
    }
    
    /**
     * Gets path to controllers folder.
     *
     * @return string
     */
    public function getControllersPath(): string
    {
        return $this->controllerPath;
    }
    
    /**
     * Gets path to view resolvers folder.
     *
     * @return string
     */
    public function getViewResolversPath(): string
    {
        return $this->viewResolversPath;
    }
    
    /**
     * Gets path to views folder.
     *
     * @return string
     */
    public function getViewsPath(): string
    {
        return $this->viewsPath;
    }
    
    /**
     * Gets path to public folder. Contents of this folder are directly available to outside world.
     *
     * @return string
     */
    public function getPublicPath(): string
    {
        return $this->publicPath;
        ;
    }
    
    /**
     * Gets whether or not application uses auto routing.
     *
     * @return boolean
     * 		true: Controllers will be automatically discovered based on route requested
     * 		false: Routes to controllers have been explicitly set in routes:route @ XML.
     */
    public function getAutoRouting(): bool
    {
        return $this->autoRouting;
    }
    
    /**
     * Gets value of application version. Value, if exists, should be sent to views and used to force refresh of JS/CSS files on clients' browsers. Example:
     * http://www.example.com/foo/bar.js?ver=1.2.0
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
    
    /**
     * Gets tag based on name from main XML root or referenced XML file if "ref" attribute was set
     *
     * @param string $name
     * @throws Exception If "ref" points to a nonexistent file.
     * @return \SimpleXMLElement
     */
    public function getTag(string $name): \SimpleXMLElement
    {
        $xml = $this->simpleXMLElement->{$name};
        $xmlFilePath = (string) $xml["ref"];
        if ($xmlFilePath) {
            $xmlFilePath .= ".xml";
            if (!file_exists($xmlFilePath)) {
                throw new Exception("XML file not found: ".$xmlFilePath);
            }
            $subXML = simplexml_load_file($xmlFilePath);
            return $subXML->{$name};
        } else {
            return $xml;
        }
    }
    
    /**
     * Gets routes detected by optional url
     *
     * @param string $url
     * @return Route[string]|NULL|Route
     */
    public function routes(string $url="")
    {
        if (!$url) {
            return $this->routes;
        } else {
            return (isset($this->routes[$url])?$this->routes[$url]:null);
        }
    }
    
    /**
     * Gets display formats detected by name
     *
     * @param string $name
     * @return Format[string]|NULL|Format
     */
    public function formats(string $name="")
    {
        if (!$name) {
            return $this->formats;
        } else {
            return (isset($this->formats[$name])?$this->formats[$name]:null);
        }
    }
}
