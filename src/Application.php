<?php
require_once("exceptions/ServletApplicationException.php");
require_once("AttributesFactory.php");
require_once("application/Route.php");
require_once("application/Format.php");

/**
 * Compiles information about application.
 */
class Application extends AttributesFactory {
	/**
	 * @var SimpleXMLElement
	 */
	private $objSimpleXMLElement;
	private	$strDefaultCharacterEncoding, $strDefaultPage, $strDefaultExtension, $strControllerPath, $strListenerPath, $strWrapperPath, $strViewsPath, $strPublicPath, $blnAutoRouting;
	private $tblListeners = array(), $tblRoutes = array(), $tblFormats = array();
	
	/**
	 * Populates attributes based on an XML file
	 * 
	 * @param string $strURL XML file url
	 */
	public function __construct($strURL) {
		if(!file_exists($strURL)) throw new ServletApplicationException("XML configuration file not found!");
		$this->objSimpleXMLElement = simplexml_load_file($strURL);
		
		$this->setDefaultPage();
		$this->setDefaultExtension();
		$this->setDefaultCharacterEncoding();
		$this->setControllersPath();
		$this->setListenersPath();
		$this->setWrappersPath();
		$this->setViewsPath();
		$this->setPublicPath();
		$this->setAutoRouting();
		$this->setListeners();
		if(!$this->blnAutoRouting) {
			$this->setRoutes();
		}		
		$this->setFormats();
	}
	
	/**
	 * Sets default character encoding. Maps to application.default_character_encoding @ XML.
	 */
	private function setDefaultCharacterEncoding() {
		$this->strDefaultCharacterEncoding = (string) $this->objSimpleXMLElement->application->default_character_encoding;
	}
	
	/**
	 * Gets default character encoding.
	 * 
	 * @return string
	 */
	public function getDefaultCharacterEncoding() {
		return $this->strDefaultCharacterEncoding;
	}

	/**
	 * Sets default landing page. Maps to application.default_page @ XML.
	 */
	private function setDefaultPage() {
		$this->strDefaultPage = (string) $this->objSimpleXMLElement->application->default_page;
		if(!$this->strDefaultPage) throw new ServletApplicationException("Parameter is mandatory: application.default_page");		
	}

	/**
	 * Gets default landing page.
	 *
	 * @return string
	 */
	public function getDefaultPage() {
		return $this->strDefaultPage;
	}

	/**
	 * Sets default file format. Maps to application.default_extension @ XML.
	 */
	private function setDefaultExtension() {
		$this->strDefaultExtension = (string) $this->objSimpleXMLElement->application->default_extension;
		if(!$this->strDefaultExtension) throw new ServletApplicationException("Parameter is mandatory: application.default_extension");
	}

	/**
	 * Gets default file format.
	 *
	 * @return string
	 */
	public function getDefaultExtension() {
		return $this->strDefaultExtension;
	}

	/**
	 * Sets path to controllers folder. Maps to application.paths.controllers @ XML.
	 */
	private function setControllersPath() {
		$this->strControllerPath = (string) $this->objSimpleXMLElement->application->paths->controllers;
	}

	/**
	 * Gets path to controllers folder.
	 *
	 * @return string
	 */
	public function getControllersPath() {
		return $this->strControllerPath;
	}

	/**
	 * Sets path to listeners folder. Maps to application.paths.controllers @ XML.
	 */
	private function setListenersPath() {
		$this->strListenerPath = (string) $this->objSimpleXMLElement->application->paths->listeners;
	}

	/**
	 * Gets path to listeners folder.
	 *
	 * @return string
	 */
	public function getListenersPath() {
		return $this->strListenerPath;
	}

	/**
	 * Sets wrappers folder. Maps to application.paths.wrappers @ XML.
	 */
	private function setWrappersPath() {
		$this->strWrapperPath = (string) $this->objSimpleXMLElement->application->paths->wrappers;
	}

	/**
	 * Gets path to wrappers folder.
	 *
	 * @return string
	 */
	public function getWrappersPath() {
		return $this->strWrapperPath;
	}

	/**
	 * Sets views folder. Maps to application.paths.views @ XML.
	 */
	private function setViewsPath() {
		$this->strViewsPath = (string) $this->objSimpleXMLElement->application->paths->views;
	}

	/**
	 * Gets path to views folder.
	 *
	 * @return string
	 */
	public function getViewsPath() {
		return $this->strViewsPath;
	}

	/**
	 * Sets public folder. Maps to application.paths.public @ XML.
	 */
	private function setPublicPath() {
		$this->strPublicPath = (string) $this->objSimpleXMLElement->application->paths->public;
	}

	/**
	 * Gets path to public folder. Contents of this folder are directly available to outside world.
	 *
	 * @return string
	 */
	public function getPublicPath() {
		return $this->strPublicPath;;
	}

	/**
	 * Sets auto routing. Maps to application.auto_routing @ XML.
	 * 		ON: Controllers will be automatically discovered based on route requested
	 * 		OFF: Routes to controllers have been explicitly set in routes:route @ XML.
	 */
	private function setAutoRouting() {		
		$this->blnAutoRouting = (int) $this->objSimpleXMLElement->application->auto_routing;
	}
	
	/**
	 * Gets whether or not application uses auto routing.
	 * 
	 * @return boolean
	 * 		true: Controllers will be automatically discovered based on route requested
	 * 		false: Routes to controllers have been explicitly set in routes:route @ XML.
	 */
	public function getAutoRouting() {		
		return $this->blnAutoRouting;
	}
	
	/**
	 * Sets user-defined listeners. Maps to listeners:listener list @ XML.
	 */
	private function setListeners() {
		$tblTMP = (array) $this->objSimpleXMLElement->listeners->listener;
		foreach($tblTMP as $tblInfo) {
			if(empty($tblInfo['class'])) throw new ServletApplicationException("Property not set: listeners->listener['class']");
			$this->tblListeners[] = (string) $tblInfo['class'];
		}
	}
	
	/**
	 * Gets user-defined listeners. They will be executed in exactly the order set by user.
	 * 
	 * @return array(string)	List of class names
	 */
	public function getListeners() {
		return $this->tblListeners;
	}

	/**
	 * Sets user-defined routes that link to controllers. Maps to routes:route list @ XML. Each route item has two fields:
	 * - url: relative path requested
	 * - class: controller class name (by convention same as file name)
	 * NOTICE: Only executed when auto_routing=0 
	 */
	private function setRoutes() {
		$tblTMP = (array) $this->objSimpleXMLElement->routes;
		if(empty($tblTMP["route"])) throw new ServletApplicationException("No routes set: routes.route");
		$tblTMP = $tblTMP["route"];
		foreach($tblTMP as $tblInfo) {
			if(empty($tblInfo['url'])) throw new ServletApplicationException("Property not set: routes->route['url']");
			if(empty($tblInfo['class'])) throw new ServletApplicationException("Property not set: routes->route['class']");
			$strUrl = (string) $tblInfo['url'];
			$this->tblRoutes[$strUrl] = new Route($strUrl, (string) $tblInfo['class']);
		}
		if(empty($this->tblRoutes)) throw new ServletApplicationException("No routes set: routes.route");
	}
	
	/**
	 * Gets user-defined routes that link to controllers.
	 * 
	 * @return array(string:Route)	Map of encapsulated routes indexed by url. 	
	 */
	public function getRoutes() {
		return $this->tblRoutes;
	}
	
	/**
	 * Gets route info based on argument path.
	 * 
	 * @param string $strURL
	 * @return Route
	 */
	public function getRouteInfo($strURL) {
		if(!isset($this->tblRoutes[$strURL])) throw new ServletApplicationException("Route not found for: ".$strURL);
		return $this->tblRoutes[$strURL];
	}
	
	/**
	 * Checks whether or not there is a route defined for argument path.
	 * 
	 * @param string $strURL
	 * @return boolean
	 */
	public function hasRoute($strURL) {
		return isset($this->tblRoutes[$strURL]);
	}

	/**
	 * Sets user-defined file formats used by application. Maps to formats:format list @ XML. Each format item has three fields:
	 * - format: file format / extension
	 * - content_type: content type that corresponds to above file format
	 * - wrapper: (optional) wrapper class name. If not set, framework-defined ViewWrapper will be used.
	 */
	private function setFormats() {
		$tblTMP = (array) $this->objSimpleXMLElement->formats;
		if(empty($tblTMP["format"])) throw new ServletApplicationException("No formats set: formats.format");
		$tblTMP = $tblTMP["format"];
		if(!is_array($tblTMP)) $tblTMP = array($tblTMP);
		foreach($tblTMP as $tblInfo) {
			if(empty($tblInfo['extension'])) throw new ServletApplicationException("Property not set: formats->format['extension']");
			if(empty($tblInfo['content_type'])) throw new ServletApplicationException("Property not set: formats->format['content_type']");
			$strExtension = (string) $tblInfo['extension'];
			$this->tblFormats[$strExtension] = new Format($strExtension, (string) $tblInfo['content_type'], (isset($tblInfo['class'])?(string) $tblInfo['class']:""));
		}
		if(empty($this->tblFormats)) throw new ServletApplicationException("No formats set: formats.format");
	}


	/**
	 * Gets user-defined file formats used by application.
	 * 
	 * return array(string:Format)	Map of encapsulated formats indexed by extension.
	 */
	public function getFormats() {
		return $this->tblFormats;
	}

	/**
	 * Gets file format info based on argument path.
	 *
	 * @param string $strExtension
	 * @return Format
	 */
	public function getFormatInfo($strExtension) {
		if(!isset($this->tblFormats[$strExtension])) throw new ServletApplicationException("Format not found for: ".$strExtension);
		return $this->tblFormats[$strExtension];
	}


	/**
	 * Checks whether or not there is an extension defined for argument path.
	 *
	 * @param string $strExtension
	 * @return boolean
	 */
	public function hasFormat($strExtension) {
		return isset($this->tblFormats[$strExtension]);
	}
}
