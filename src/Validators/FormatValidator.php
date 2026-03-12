<?php

namespace Lucinda\STDOUT\Validators;

use Lucinda\STDOUT\Application;
use Lucinda\MVC\XmlReader\Exception;

/**
 * Binds information in 'application', 'formats' and 'routes' XML tags based on route requested to detect final response format
 */
class FormatValidator
{
    private string $format;

    /**
     * Performs detection process
     *
     * @param  Application $application
     * @param  string      $url
     * @throws Exception
     */
    public function __construct(Application $application, string $url)
    {
        $extension = $application->getApplicationInfo()->getDefaultFormat();
        $route = $application->getRoutes($url); // assumes $url has already been validated
        if ($route->getFormat()) {
            $extension = $route->getFormat();
        }

        if ($application->getResolvers($extension)===null) {
            throw new Exception("Format could not be matched to formats.format tag @ XML: ".$extension);
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
