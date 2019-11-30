<?php
namespace Lucinda\STDOUT\EventListeners;

use Lucinda\STDOUT\FormatNotFoundException;
use Lucinda\STDOUT\PathNotFoundException;

/**
 * Validates request data based on Application and Request objects and saves results to Attributes
 */
class RequestValidator extends Request
{
    /**
     * Performs request validation
     */
    public function run()
    {
        $url = $this->request->getURI()->getPage();
        if ($url=="") {
            $url = $this->application->getDefaultPage();
        }
        $extension = $this->application->getDefaultFormat();
        if (!$this->application->getAutoRouting()) {
            if ($this->application->routes($url)===null) {
                $matchFound = false;
                $routes = $this->application->routes();
                foreach ($routes as $route) {
                    if (strpos($route->getPath(), "(")!==false) {
                        $matches = [];
                        preg_match_all("/(\(([^)]+)\))/", $route->getPath(), $matches);
                        $names = $matches[2];
                        $pattern = "/^".str_replace($matches[1], "([^\/]+)", str_replace("/", "\/", $route->getPath()))."$/";
                        $results = [];
                        if (preg_match_all($pattern, $url, $results)==1) {
                            $parameters = [];
                            foreach ($results as $i=>$item) {
                                if ($i==0) {
                                    continue;
                                }
                                $parameters[$names[$i-1]]=$item[0];
                            }
                            $this->attributes->setPathParameters($parameters);
                            if ($route->getFormat()) {
                                $extension = $route->getFormat();
                            }
                            $url = $route->getPath();
                            $matchFound = true;
                            break;
                        }
                    }
                }
                if (!$matchFound) {
                    throw new PathNotFoundException("Route could not be matched to routes.route tag @ XML: ".$url);
                }
            } else {
                $route = $this->application->routes($url);
                if ($route->getFormat()) {
                    $extension = $route->getFormat();
                }
            }
        }
        $this->attributes->setRequestedPage($url);

        if ($this->application->formats($extension)===null) {
            throw new FormatNotFoundException("Format could not be matched to formats.format tag @ XML: ".$extension);
        } 
        
        $this->attributes->setRequestedResponseFormat($extension);
    }
}
