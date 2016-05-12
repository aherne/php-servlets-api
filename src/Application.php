<?php
/**
 * Compiles information about application.
 */
class Application extends AttributesFactory {
	/**
	 * @var SimpleXMLElement
	 */
	private $objSimpleXMLElement;
	private	$strDefaultCharacterEncoding, $strDefaultPage, $strDefaultExtension, $strControllerPath, $strListenerPath, $strWrapperPath, $strViewsPath, $blnAutoRouting;
	private $tblListeners = array(), $tblRoutes = array(), $tblFormats = array();
	
	/**
	 * Populates attributes from an XML file
	 * 
	 * @param string $strURL XML file url
	 */
	public function __construct($strURL) {
		if(!file_exists($strURL)) throw new ServletException("Application file not defined!");
		$this->objSimpleXMLElement = simplexml_load_file($strURL);
		
		$this->setDefaultPage();
		$this->setDefaultExtension();
		$this->setDefaultCharacterEncoding();
		$this->setControllersPath();
		$this->setListenersPath();
		$this->setWrappersPath();
		$this->setViewsPath();
		$this->setAutoRouting();
		$this->setListeners();
		if(!$this->blnAutoRouting) {
			$this->setRoutes();
		}		
		$this->setFormats();
	}
	
	private function setDefaultCharacterEncoding() {
		$this->strDefaultCharacterEncoding = (string) $this->objSimpleXMLElement->application->default_character_encoding;
	}
	
	public function getDefaultCharacterEncoding() {
		return $this->strDefaultCharacterEncoding;
	}
	
	private function setDefaultPage() {
		$this->strDefaultPage = (string) $this->objSimpleXMLElement->application->default_page;
		if(!$this->strDefaultPage) throw new ServletApplicationException("Parameter is mandatory: application.default_page");		
	}
	
	public function getDefaultPage() {
		return $this->strDefaultPage;
	}
	
	private function setDefaultExtension() {
		$this->strDefaultExtension = (string) $this->objSimpleXMLElement->application->default_extension;
		if(!$this->strDefaultExtension) throw new ServletApplicationException("Parameter is mandatory: application.default_extension");
	}
	
	public function getDefaultExtension() {
		return $this->strDefaultExtension;
	}
	
	private function setControllersPath() {
		$this->strControllerPath = (string) $this->objSimpleXMLElement->application->paths->controllers;
	}
	
	public function getControllersPath() {
		return $this->strControllerPath;
	}
	
	private function setListenersPath() {
		$this->strListenerPath = (string) $this->objSimpleXMLElement->application->paths->listeners;
	}
	
	public function getListenersPath() {
		return $this->strListenerPath;
	}
	
	private function setWrappersPath() {
		$this->strWrapperPath = (string) $this->objSimpleXMLElement->application->paths->wrappers;
	}
	
	public function getWrappersPath() {
		return $this->strWrapperPath;
	}
	
	private function setViewsPath() {
		$this->strViewsPath = (string) $this->objSimpleXMLElement->application->paths->views;
	}
	
	public function getViewsPath() {
		return $this->strViewsPath;
	}
	
	private function setAutoRouting() {		
		$this->blnAutoRouting = (int) $this->objSimpleXMLElement->application->auto_routing;
	}
	
	public function getAutoRouting() {		
		return $this->blnAutoRouting;
	}
	
	private function setListeners() {
		$tblTMP = (array) $this->objSimpleXMLElement->listeners->listener;
		foreach($tblTMP as $tblInfo) {
			if(empty($tblInfo['class'])) throw new ServletApplicationException("Property not set: listeners->listener['class']");
			$this->tblListeners[] = (string) $tblInfo['class'];
		}
	}
	
	public function getListeners() {
		return $this->tblListeners;
	}
	
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
	
	public function getRoutes() {
		return $this->tblRoutes;
	}
	
	public function getRouteInfo($strURL) {
		return (isset($this->tblRoutes[$strURL])?$this->tblRoutes[$strURL]:array());
	}
	
	public function hasRoute($strURL) {
		return isset($this->tblRoutes[$strURL]);
	}
	
	private function setFormats() {
		$tblTMP = (array) $this->objSimpleXMLElement->formats;
		if(empty($tblTMP["format"])) throw new ServletApplicationException("No formats set: formats.format");
		$tblTMP = $tblTMP["format"];
		foreach($tblTMP as $tblInfo) {
			if(empty($tblInfo['extension'])) throw new ServletApplicationException("Property not set: formats->format['extension']");
			if(empty($tblInfo['content_type'])) throw new ServletApplicationException("Property not set: formats->format['content_type']");
			$strExtension = (string) $tblInfo['extension'];
			$this->tblFormats[$strExtension] = new Format($strExtension, (string) $tblInfo['content_type'], (isset($tblInfo['class'])?(string) $tblInfo['class']:""));
		}
		if(empty($this->tblFormats)) throw new ServletApplicationException("No formats set: formats.format");
	}
	
	public function getFormats() {
		return $this->tblFormats;
	}
	
	public function getFormatInfo($strExtension) {
		return (isset($this->tblFormats[$strExtension])?$this->tblFormats[$strExtension]:array());
	}
	
	public function hasFormat($strExtension) {
		return isset($this->tblFormats[$strExtension]);
	}
}

class Route {
	private $strPath, $strControllerClass;
	
	public function __construct($strPath, $strControllerClass) {
		$this->strPath = $strPath;
		$this->strControllerClass = $strControllerClass;
	}
	
	public function getPath() {
		return $this->strPath;
	}
	
	public function getController() {
		return $this->strControllerClass;
	}
}

class Format {
	private $strExtension, $strContentType, $strWrapperClass;
	
	public function __construct($strExtension, $strContentType, $strWrapperClass="") {
		$this->strExtension = $strExtension;
		$this->strContentType = $strContentType;
		$this->strWrapperClass = $strWrapperClass;
	}
	
	public function getExtension() {
		return $this->strExtension;
	}
	
	public function getContentType() {
		return $this->strContentType;
	}
	
	public function getWrapper() {
		return $this->strWrapperClass;
	}
}