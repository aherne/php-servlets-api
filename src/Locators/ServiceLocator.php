<?php
namespace Lucinda\STDOUT\Locators;

use Lucinda\STDOUT\Application;
use Lucinda\STDOUT\Attributes;
use Lucinda\STDOUT\Exception;
use Lucinda\STDOUT\XMLException;

/**
 * Blueprint for locating a service class name on disk based on given criteria
 */
abstract class ServiceLocator
{
    protected $className;
    
    /**
     * Triggers service location.
     *
     * @param Application $application
     * @param Attributes $attributes
     * @throws Exception If controller file could not be located on disk.
     * @throws XMLException If XML is misconfigured
     */
    public function __construct(Application $application, Attributes $attributes): void
    {
        $this->setClassName($application, $attributes);
    }
    
    /**
     * Locates service on disk based on criteria
     *
     * @param Application $application
     * @param Attributes $attributes
     */
    abstract protected function setClassName(Application $application, Attributes $attributes): void;
    
    /**
     * Gets service class name.
     *
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }
}
