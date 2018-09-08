<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Single responsibility in validating requested page based on configuration.xml encapsulated by Application object
 */
class PageValidator implements RequestValidator {
	private $page;
	private $contentType;
	private $pathParameters=array();
	
	/**
	 * @param string $page 
	 * @param Application $application
	 * @throws PathNotFoundException
     * @throws ServletException
	 */
	public function __construct($page, Application $application) {
        $this->validate($application, $page);
	}
	
	/**
	 * Detects requested page, format & path parameters by matching routes/formats in xml to requested route.
	 * 
	 * @param Application $application
	 * @param string $url
	 * @throws PathNotFoundException
     * @throws FormatNotFoundException
	 */
	private function validate(Application $application, $url) {
		if($url=="") {
			$url = $application->getDefaultPage();
		}
		$extension = $application->getDefaultExtension();
		if(!$application->getAutoRouting()) {
		    if(!$application->routes()->contains($url)) {
				$matchFound = false;
				$routes = $application->routes()->toArray();
				foreach($routes as $route) {
					if(strpos($route->getPath(), "(")!==false) {
						preg_match_all("/(\(([^)]+)\))/", $route->getPath(), $matches);
						$names = $matches[2];
						$pattern = "/^".str_replace($matches[1],"([^\/]+)",str_replace("/","\/",$route->getPath()))."$/";
						if(preg_match_all($pattern,$url,$results)==1) {
							foreach($results as $i=>$item) {
								if($i==0) continue;
								$this->pathParameters[$names[$i-1]]=$item[0];
							}
							if($route->getFormat()) {
							    $extension = $route->getFormat();
                            }
							$url = $route->getPath();
							$matchFound = true;
							break;
						}
					}
				}
				if(!$matchFound) throw new PathNotFoundException("Route could not be matched to routes.route tag @ XML: ".$url);
			} else {
                $route = $application->routes()->get($url);
			    if($route->getFormat()) {
                    $extension = $route->getFormat();
                }
            }
		}
		$this->page = $url;

		if(!$application->formats()->contains($extension)) throw new FormatNotFoundException("Format could not be matched to formats.format tag @ XML: ".$extension);
        $format = $application->formats()->get($extension);
        $this->contentType = $format->getContentType().($format->getCharacterEncoding()?"; charset=".$format->getCharacterEncoding():"");
	}
	
	/**
	 * {@inheritDoc}
	 * @see RequestValidator::getPage()
	 */
	public function getPage() {
		return $this->page;
	}
	
	/**
	 * {@inheritDoc}
	 * @see RequestValidator::getPathParameter()
	 */
	public function getPathParameter($name) {
		return (isset($this->pathParameters[$name])?$this->pathParameters[$name]:null);
	}
	
	/**
	 * {@inheritDoc}
	 * @see RequestValidator::getPathParameters()
	 */
	public function getPathParameters() {
		return $this->pathParameters;
	}

    /**
     * {@inheritDoc}
     * @see RequestValidator::getPathParameters()
     */
    public function getContentType() {
        return $this->contentType;
    }
}
