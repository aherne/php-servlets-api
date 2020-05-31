<?php
namespace Lucinda\STDOUT;

use Lucinda\STDOUT\Application\Route;
use Lucinda\STDOUT\Application\Format;
use Lucinda\STDOUT\Session\Options as SessionOptions;
use Lucinda\STDOUT\Cookies\Options as CookiesOptions;

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
    private $validatorsPath;
    private $autoRouting;
    private $version;
    private $routes = array();
    private $formats = array();
    private $sessionOptions;
    private $cookiesOptions;
    
    /**
     * Populates attributes based on an XML file
     *
     * @param string $xmlFilePath XML file url
     * @throws ConfigurationException If xml content has failed validation.
     */
    public function __construct(string $xmlFilePath)
    {
        if (!file_exists($xmlFilePath)) {
            throw new ConfigurationException("XML file not found: ".$xmlFilePath);
        }
        $this->simpleXMLElement = simplexml_load_file($xmlFilePath);
        
        $this->setApplicationInfo();
        
        if (!$this->autoRouting) {
            $this->setRoutes();
        }
        
        $this->setFormats();
        
        $this->setSessionOptions();
        
        $this->setCookieOptions();
    }
    
    /**
     * Sets basic application info based on contents of "application" XML tag
     * @throws ConfigurationException If xml content has failed validation.
     */
    private function setApplicationInfo(): void
    {
        $xml = $this->getTag("application");
        if (empty($xml)) {
            throw new ConfigurationException("Tag is mandatory: application");
        }
        $this->defaultPage = (string) $xml["default_page"];
        if (!$this->defaultPage) {
            throw new ConfigurationException("Attribute 'default_page' is mandatory for 'application' tag");
        }
        $this->defaultFormat = (string) $xml["default_format"];
        if (!$this->defaultFormat) {
            throw new ConfigurationException("Attribute 'default_format' is mandatory for 'application' tag");
        }
        $this->autoRouting = (int) $xml["auto_routing"];
        $this->version = (string) $xml["version"];
        $this->controllerPath = (string) $xml->paths["controllers"];
        $this->viewResolversPath = (string) $xml->paths["resolvers"];
        $this->validatorsPath = (string) $xml->paths["validators"];
        $this->viewsPath = (string) $xml->paths["views"];
    }
    
    /**
     * Sets user-defined routes that map to possible requested pages based on contents of "routes" XML tag
     * NOTICE: Only executed when auto_routing=0
     * @throws ConfigurationException If xml content has failed validation.
     */
    private function setRoutes(): void
    {
        $xml = $this->simpleXMLElement->routes;
        if ($xml===null) {
            throw new ConfigurationException("Tag 'routes' is mandatory");
        }
        $list = $xml->xpath("//route");
        foreach ($list as $info) {
            $url = (string) $info["url"];
            if (!$url) {
                throw new ConfigurationException("Attribute 'url' is mandatory for 'route' tag");
            }
            $this->routes[$url] = new Route($info);
        }
        if (empty($this->routes)) {
            throw new ConfigurationException("Tag 'routes' is empty");
        }
    }
    
    /**
     * Sets user-defined file response formats that will be used by application based on contents of "formats" XML tag
     * @throws ConfigurationException If xml content has failed validation.
     */
    private function setFormats(): void
    {
        $xml = $this->simpleXMLElement->formats;
        if ($xml===null) {
            throw new ConfigurationException("Tag 'formats' is mandatory");
        }
        $list = $xml->xpath("//format");
        foreach ($list as $info) {
            $name = (string) $info["name"];
            if (!$name) {
                throw new ConfigurationException("Attribute 'name' is mandatory for 'format' tag");
            }
            $this->formats[$name] = new Format($info);
        }
        if (empty($this->formats)) {
            throw new ConfigurationException("Tag 'formats' is empty");
        }
    }
    
    /**
     * Sets options to start session with based on "session" XML tag
     */
    private function setSessionOptions(): void
    {
        $xml = $this->simpleXMLElement->session;
        if ($xml===null) {
            return;
        }
        $this->sessionOptions = new SessionOptions($xml);
    }
    
    /**
     * Sets options to create cookies with based on "cookies" XML tag
     */
    private function setCookieOptions(): void
    {
        $xml = $this->simpleXMLElement->cookies;
        if ($xml===null) {
            return;
        }
        $this->cookiesOptions = new CookiesOptions($xml);
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
     * Gets path to parameter validators folder.
     *
     * @return string
     */
    public function getValidatorsPath(): string
    {
        return $this->validatorsPath;
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
     * Gets  options to start session with based on "session" XML tag
     *
     * @return SessionOptions|NULL
     */
    public function getSessionOptions(): ?SessionOptions
    {
        return $this->sessionOptions;
    }
    
    /**
     * Gets options to create cookies with based on "cookies" XML tag
     *
     * @return CookiesOptions|NULL
     */
    public function getCookieOptions(): ?CookiesOptions
    {
        return $this->cookiesOptions;
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
     * @throws ConfigurationException If "ref" points to a nonexistent file.
     * @return \SimpleXMLElement
     */
    public function getTag(string $name): \SimpleXMLElement
    {
        $xml = $this->simpleXMLElement->{$name};
        $xmlFilePath = (string) $xml["ref"];
        if ($xmlFilePath) {
            $xmlFilePath .= ".xml";
            if (!file_exists($xmlFilePath)) {
                throw new ConfigurationException("XML file not found: ".$xmlFilePath);
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
     * @return Route|array|null
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
     * @return Format|array|null
     */
    public function formats(string $name="")
    {
        if (!$name) {
            return $this->formats;
        } else {
            return (isset($this->formats[$name])?$this->formats[$name]:null);
        }
    }

    /**
     * Gets root XML tag
     *
     * @return \SimpleXMLElement
     */
    public function getXML(): \SimpleXMLElement
    {
        return $this->simpleXMLElement;
    }
}
