<?php
namespace Lucinda\MVC\STDOUT;

require_once("exceptions/ApplicationException.php");
require_once("AttributesFactory.php");
require_once("application/Route.php");
require_once("application/Format.php");

/**
 * Compiles information about application.
 */
class Application extends AttributesFactory {
	/**
	 * @var \SimpleXMLElement
	 */
	private $simpleXMLElement;
	private	$defaultPage, $defaultExtension, $controllerPath, $listenerPath, $wrapperPath, $viewsPath, $publicPath, $autoRouting, $version;
	private $listeners = array(), $routes = array(), $formats = array();
	
	/**
	 * Populates attributes based on an XML file
	 * 
	 * @param string $uRL XML file url
	 */
	public function __construct($uRL) {
		if(!file_exists($uRL)) throw new ApplicationException("XML configuration file not found!");
		$this->simpleXMLElement = simplexml_load_file($uRL);
		
		$this->setDefaultPage();
		$this->setDefaultExtension();
		$this->setControllersPath();
		$this->setListenersPath();
		$this->setWrappersPath();
		$this->setViewsPath();
		$this->setPublicPath();
		$this->setAutoRouting();
		$this->setVersion();
		$this->setListeners();
		if(!$this->autoRouting) {
			$this->setRoutes();
		}		
		$this->setFormats();
	}

	/**
	 * Sets default landing page. Maps to application.default_page @ XML.
	 */
	private function setDefaultPage() {
		$this->defaultPage = (string) $this->simpleXMLElement->application->default_page;
		if(!$this->defaultPage) throw new ApplicationException("XML tag is mandatory: application.default_page");		
	}

	/**
	 * Gets default landing page.
	 *
	 * @return string
	 */
	public function getDefaultPage() {
		return $this->defaultPage;
	}

	/**
	 * Sets default file format. Maps to application.default_extension @ XML.
	 */
	private function setDefaultExtension() {
		$this->defaultExtension = (string) $this->simpleXMLElement->application->default_extension;
		if(!$this->defaultExtension) throw new ApplicationException("XML tag is mandatory: application.default_extension");
	}

	/**
	 * Gets default file format.
	 *
	 * @return string
	 */
	public function getDefaultExtension() {
		return $this->defaultExtension;
	}

	/**
	 * Sets path to controllers folder. Maps to application.paths.controllers @ XML.
	 */
	private function setControllersPath() {
		$this->controllerPath = (string) $this->simpleXMLElement->application->paths->controllers;
	}

	/**
	 * Gets path to controllers folder.
	 *
	 * @return string
	 */
	public function getControllersPath() {
		return $this->controllerPath;
	}

	/**
	 * Sets path to listeners folder. Maps to application.paths.controllers @ XML.
	 */
	private function setListenersPath() {
		$this->listenerPath = (string) $this->simpleXMLElement->application->paths->listeners;
	}

	/**
	 * Gets path to listeners folder.
	 *
	 * @return string
	 */
	public function getListenersPath() {
		return $this->listenerPath;
	}

	/**
	 * Sets wrappers folder. Maps to application.paths.wrappers @ XML.
	 */
	private function setWrappersPath() {
		$this->wrapperPath = (string) $this->simpleXMLElement->application->paths->wrappers;
	}

	/**
	 * Gets path to wrappers folder.
	 *
	 * @return string
	 */
	public function getWrappersPath() {
		return $this->wrapperPath;
	}

	/**
	 * Sets views folder. Maps to application.paths.views @ XML.
	 */
	private function setViewsPath() {
		$this->viewsPath = (string) $this->simpleXMLElement->application->paths->views;
	}

	/**
	 * Gets path to views folder.
	 *
	 * @return string
	 */
	public function getViewsPath() {
		return $this->viewsPath;
	}

	/**
	 * Sets public folder. Maps to application.paths.public @ XML.
	 */
	private function setPublicPath() {
		$this->publicPath = (string) $this->simpleXMLElement->application->paths->public;
	}

	/**
	 * Gets path to public folder. Contents of this folder are directly available to outside world.
	 *
	 * @return string
	 */
	public function getPublicPath() {
		return $this->publicPath;;
	}

	/**
	 * Sets auto routing. Maps to application.auto_routing @ XML.
	 * 		ON: Controllers will be automatically discovered based on route requested
	 * 		OFF: Routes to controllers have been explicitly set in routes:route @ XML.
	 */
	private function setAutoRouting() {		
		$this->autoRouting = (int) $this->simpleXMLElement->application->auto_routing;
	}
	
	/**
	 * Gets whether or not application uses auto routing.
	 * 
	 * @return boolean
	 * 		true: Controllers will be automatically discovered based on route requested
	 * 		false: Routes to controllers have been explicitly set in routes:route @ XML.
	 */
	public function getAutoRouting() {		
		return $this->autoRouting;
	}
	
	/**
	 * Sets application version. Value should be sent to views and used to force refresh of JS/CSS files on clients' browsers. Example:
	 * http://www.example.com/foo/bar.js?ver=APPLICATION_VERSION 
	 */
	private function setVersion() {
	    $this->version = (string) $this->simpleXMLElement->application->version;
	}
	
	/**
	 * Gets value of application version. Value, if exists, should be sent to views and used to force refresh of JS/CSS files on clients' browsers. Example:
	 * http://www.example.com/foo/bar.js?ver=1.2.0 
	 * 
	 * @return string
	 */
	public function getVersion() {
	    return $this->version;
	}
	
	/**
	 * Sets user-defined listeners. Maps to listeners:listener list @ XML.
	 */
	private function setListeners() {
		$tmp = (array) $this->simpleXMLElement->listeners;
		if(empty($tmp["listener"])) return;
		$tmp = $tmp["listener"];
		if(!is_array($tmp)) $tmp = array($tmp);
		foreach($tmp as $info) {
			if(empty($info['class'])) throw new ApplicationException("XML property is mandatory: listeners.listener['class']");
			$this->listeners[] = (string) $info['class'];
		}
	}
	
	/**
	 * Gets user-defined listeners. They will be executed in exactly the order set by user.
	 * 
	 * @return array(string)	List of class names
	 */
	public function getListeners() {
		return $this->listeners;
	}

	/**
	 * Sets user-defined routes that link to controllers. Maps to routes:route list @ XML. Each route item has two fields:
	 * - url: (mandatory)relative path requested
	 * - controller: controller class name, incl path (by convention same as file name)
     * - view: view file path, without extension
     * - extension: route-specific response format
	 * NOTICE: Only executed when auto_routing=0 
	 */
	private function setRoutes() {
		$tmp = (array) $this->simpleXMLElement->routes;
		if(empty($tmp["route"])) throw new ApplicationException("XML tag is mandatory: routes.route");
		$tmp = $tmp["route"];
		if(!is_array($tmp)) $tmp = array($tmp);
		foreach($tmp as $info) {
			if(empty($info['url'])) throw new ApplicationException("XML property is mandatory: routes.route['url']");
			$url = (string) $info['url'];
			$this->routes[$url] = new Route($url, (string) $info['controller'], (string) $info['view'], (string) $info['extension']);
		}
		if(empty($this->routes)) throw new ApplicationException("XML tag cannot be empty: routes");
	}
	
	/**
	 * Gets user-defined routes that link to controllers.
	 * 
	 * @return array(string:Route)	Map of encapsulated routes indexed by url. 	
	 */
	public function getRoutes() {
		return $this->routes;
	}
	
	/**
	 * Gets route info based on argument path.
	 * 
	 * @param string $uRL
	 * @return Route
	 * @throws PathNotFoundException In case route could not be matched in XML.
	 */
	public function getRouteInfo($uRL) {
		if(!isset($this->routes[$uRL])) throw new PathNotFoundException("Route could not be matched in routes.route tag @ XML: ".$uRL);
		return $this->routes[$uRL];
	}
	
	/**
	 * Checks whether or not there is a route defined for argument path.
	 * 
	 * @param string $uRL
	 * @return boolean
	 */
	public function hasRoute($uRL) {
		return isset($this->routes[$uRL]);
	}

	/**
	 * Sets user-defined file formats used by application. Maps to formats:format list @ XML. Each format item has three fields:
	 * - format: file format / extension
	 * - content_type: content type that corresponds to above file format
	 * - wrapper: (optional) wrapper class name. If not set, framework-defined ViewWrapper will be used.
	 */
	private function setFormats() {
		$tmp = (array) $this->simpleXMLElement->formats;
		if(empty($tmp["format"])) throw new ApplicationException("XML tag is mandatory: formats.format");
		$tmp = $tmp["format"];
		if(!is_array($tmp)) $tmp = array($tmp);
		foreach($tmp as $info) {
			if(empty($info['extension'])) throw new ApplicationException("XML property is mandatory: formats->format['extension']");
			if(empty($info['content_type'])) throw new ApplicationException("XML property is mandatory: formats->format['content_type']");
			$extension = (string) $info['extension'];
			$this->formats[$extension] = new Format(
					$extension, 
					(string) $info['content_type'],
					(isset($info['charset'])?(string) $info['charset']:""), 
					(isset($info['class'])?(string) $info['class']:""));
		}
		if(empty($this->formats)) throw new ApplicationException("XML tag cannot be empty: formats");
	}


	/**
	 * Gets user-defined file formats used by application.
	 * 
	 * return array(string:Format)	Map of encapsulated formats indexed by extension.
	 */
	public function getFormats() {
		return $this->formats;
	}

	/**
	 * Gets file format info based on argument path.
	 *
	 * @param string $extension
	 * @return Format
	 * @throws FormatNotFoundException In case format could not be matched in XML.
	 */
	public function getFormatInfo($extension) {
		if(!isset($this->formats[$extension])) throw new FormatNotFoundException("Format could not be matched in formats.format tag @ XML: ".$extension);
		return $this->formats[$extension];
	}


	/**
	 * Checks whether or not there is an extension defined for argument path.
	 *
	 * @param string $extension
	 * @return boolean
	 */
	public function hasFormat($extension) {
		return isset($this->formats[$extension]);
	}
	
	/**
	 * Gets a pointer to XML file reader.
	 * 
	 * @return \SimpleXMLElement
	 */
	public function getXML() {
	    return $this->simpleXMLElement;
	}
}
