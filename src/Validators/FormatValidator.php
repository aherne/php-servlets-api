<?php

namespace Lucinda\STDOUT\Validators;

use Lucinda\STDOUT\Application;
use Lucinda\MVC\XmlReader\Exception;

/**
 * Selects and validates the response format for a matched route
 *
 * The application default format is used unless the route overrides it. The
 * selected format must identify a configured resolver before it is exposed.
 */
final class FormatValidator
{
    /**
     * Selected resolver format identifier
     */
    private string $format;

    /**
     * Selects the route format and confirms that it has a resolver
     *
     * @param Application $application Parsed application and resolver configuration
     * @param string $url Previously validated route identifier
     * @throws Exception If the selected format has no configured resolver
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
     * Gets the validated response format
     *
     * @return string Resolver format identifier selected for the route
     */
    public function getFormat(): string
    {
        return $this->format;
    }
}
