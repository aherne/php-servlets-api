<?php
namespace Lucinda\MVC\STDOUT;

require("application/Route.php");
require("application/Format.php");

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
    private $listenerPath;
    private $viewResolversPath;
    private $viewsPath;
    private $publicPath;
    private $autoRouting;
    private $version;
    private $listeners = array();
    private $routes = array();
    private $formats = array();
    private $attributes = array();

    private $objectsCache=array();
    
    /**
     * Populates attributes based on an XML file
     *
     * @param string $xmlFilePath XML file url
     * @throws ServletException If xml file wasn't found
     * @throws XMLException If xml content has failed validation.
     */
    public function __construct($xmlFilePath)
    {
        if (!file_exists($xmlFilePath)) {
            throw new ServletException("XML file not found: ".$xmlFilePath);
        }
        $this->simpleXMLElement = simplexml_load_file($xmlFilePath);
        
        $this->setApplicationInfo();
        
        $this->setListeners();
        
        if (!$this->autoRouting) {
            $this->setRoutes();
        }
        
        $this->setFormats();
    }
    
    /**
     * Sets basic application info based on contents of "application" XML tag
     * @throws XMLException If xml content has failed validation.
     */
    private function setApplicationInfo()
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
        $this->listenerPath = (string) $xml->paths->listeners;
        $this->controllerPath = (string) $xml->paths->controllers;
        $this->viewResolversPath = (string) $xml->paths->resolvers;
        $this->viewsPath = (string) $xml->paths->views;
        $this->publicPath = (string) $xml->paths->public;
        $this->autoRouting = (int) $xml["auto_routing"];
        $this->version = (string) $xml["version"];
    }
    
    /**
     * Sets user-defined event listeners based on contents of "listeners" XML tag
     */
    private function setListeners()
    {
        $tmp = (array) $this->getTag("listeners");
        if (empty($tmp["listener"])) {
            return;
        }
        $list = (is_array($tmp["listener"])?$tmp["listener"]:[$tmp["listener"]]);
        foreach ($list as $info) {
            if (empty($info['class'])) {
                throw new XMLException("Attribute 'class' is mandatory for 'listener' tag");
            }
            $this->listeners[] = (string) $info['class'];
        }
    }
    
    /**
     * Sets user-defined routes that map to possible requested pages based on contents of "routes" XML tag
     * NOTICE: Only executed when auto_routing=0
     * @throws XMLException If xml content has failed validation.
     */
    private function setRoutes()
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
    private function setFormats()
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
    public function getDefaultPage()
    {
        return $this->defaultPage;
    }
    
    /**
     * Gets default response format name (eg: html or json)
     *
     * @return string
     */
    public function getDefaultFormat()
    {
        return $this->defaultFormat;
    }
    
    /**
     * Gets path to controllers folder.
     *
     * @return string
     */
    public function getControllersPath()
    {
        return $this->controllerPath;
    }
    
    /**
     * Gets path to listeners folder.
     *
     * @return string
     */
    public function getListenersPath()
    {
        return $this->listenerPath;
    }
    
    /**
     * Gets path to view resolvers folder.
     *
     * @return string
     */
    public function getViewResolversPath()
    {
        return $this->viewResolversPath;
    }
    
    /**
     * Gets path to views folder.
     *
     * @return string
     */
    public function getViewsPath()
    {
        return $this->viewsPath;
    }
    
    /**
     * Gets path to public folder. Contents of this folder are directly available to outside world.
     *
     * @return string
     */
    public function getPublicPath()
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
    public function getAutoRouting()
    {
        return $this->autoRouting;
    }
    
    /**
     * Gets value of application version. Value, if exists, should be sent to views and used to force refresh of JS/CSS files on clients' browsers. Example:
     * http://www.example.com/foo/bar.js?ver=1.2.0
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
    
    /**
     * Gets user-defined listeners. They will be executed in exactly the order set by user.
     *
     * @return string[]	List of class names
     */
    public function getListeners()
    {
        return $this->listeners;
    }
    
    /**
     * Gets tag based on name from main XML root or referenced XML file if "ref" attribute was set
     *
     * @param string $name
     * @throws ServletException If "ref" points to a nonexistent file.
     * @return \SimpleXMLElement
     */
    public function getTag($name)
    {
        $xml = $this->simpleXMLElement->{$name};
        $xmlFilePath = (string) $xml["ref"];
        if ($xmlFilePath) {
            if (isset($this->objectsCache[$name])) {
                return $this->objectsCache[$name];
            } else {
                $xmlFilePath = $xmlFilePath.".xml";
                if (!file_exists($xmlFilePath)) {
                    throw new Exception("XML file not found: ".$xmlFilePath);
                }
                $subXML = simplexml_load_file($xmlFilePath);
                $returningXML = $subXML->{$name};
                $this->objectsCache[$name] = $returningXML;
                return $returningXML;
            }
        } else {
            return $xml;
        }
    }
        
    /**
     * Gets or sets application attributes
     *
     * @param string $key
     * @param mixed $value
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
    
    /**
     * Gets routes detected by optional url
     *
     * @param string $url
     * @return Route[string]|NULL|Route
     */
    public function routes($url="")
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
    public function formats($name="")
    {
        if (!$name) {
            return $this->formats;
        } else {
            return (isset($this->formats[$name])?$this->formats[$name]:null);
        }
    }
}
