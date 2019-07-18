<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Single responsibility in validating requested page based on XML encapsulated by Application object
 */
class PageValidator implements RequestValidator {
    private $page;
    private $format;
    private $parameters=array();

    /**
     * Validates resource requested by client based on XML information
     *
     * @param string $page
     * @param Application $application
     * @throws PathNotFoundException If requested resource doesn't exist in XML as a "route" tag.
     * @throws FormatNotFoundException If requested response format doesn't exist in XML as a "format" tag.
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
        $extension = $application->getDefaultFormat();
        if(!$application->getAutoRouting()) {
            if($application->routes($url)===null) {
                $matchFound = false;
                $routes = $application->routes();
                foreach($routes as $route) {
                    if(strpos($route->getPath(), "(")!==false) {
                        preg_match_all("/(\(([^)]+)\))/", $route->getPath(), $matches);
                        $names = $matches[2];
                        $pattern = "/^".str_replace($matches[1],"([^\/]+)",str_replace("/","\/",$route->getPath()))."$/";
                        if(preg_match_all($pattern,$url,$results)==1) {
                            foreach($results as $i=>$item) {
                                if($i==0) continue;
                                $this->parameters[$names[$i-1]]=$item[0];
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
                $route = $application->routes($url);
                if($route->getFormat()) {
                    $extension = $route->getFormat();
                }
            }
        }
        $this->page = $url;

        if($application->formats($extension)===null) throw new FormatNotFoundException("Format could not be matched to formats.format tag @ XML: ".$extension);
        $format = $application->formats($extension);
//
        $this->format = $extension;
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
     * @see RequestValidator::parameters()
     */
    public function parameters($name="") {
        if(!$name) return $this->parameters;
        else return (isset($this->parameters[$name])?$this->parameters[$name]:null);
    }

    /**
     * {@inheritDoc}
     * @see RequestValidator::getFormat()
     */
    public function getFormat() {
        return $this->format;
    }
}
