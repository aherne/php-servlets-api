<?php
namespace Lucinda\STDOUT\EventListeners\Validators;

use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\FormatNotFoundException;

/**
 * Binds information in 'application', 'formats' and 'routes' XML tags based on route requested to detect final response format
 */
class FormatValidator
{
    private $format;
    
    /**
     * Performs detection process
     * 
     * @param Application $application
     * @param string $url
     * @throws FormatNotFoundException
     */
    public function __construct(Application $application, string $url)
    {
        $extension = $application->getDefaultFormat();
        if (!$application->getAutoRouting()) {
            $route = $application->routes($url);
            if ($route->getFormat()) {
                $extension = $route->getFormat();
            }
        }        
        
        if ($application->formats($extension)===null) {
            throw new FormatNotFoundException("Format could not be matched to formats.format tag @ XML: ".$extension);
        }
        
        $this->format = $extension;
    }
    
    /**
     * Gets final response format
     * 
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }
}

