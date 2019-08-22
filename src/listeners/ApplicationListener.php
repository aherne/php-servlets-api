<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Listens on configuration object and appends attributes to it.
 */
abstract class ApplicationListener implements Runnable
{
    protected $application;

    /**
     * Saves Application object to be available in implemented run() methods.
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }
}
