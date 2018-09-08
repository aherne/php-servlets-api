<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Listens on configuration object and appends attributes to it.
 */
abstract class ApplicationListener implements Runnable {
	protected $application;

	/**
	 * @param Application $application
	 */
	public function __construct(Application $application) {
		$this->application = $application;
	}
}