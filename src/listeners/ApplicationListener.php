<?php
/**
 * Listens on configuration object and appends attributes to it.
 */
abstract class ApplicationListener implements Runnable {
	protected $application;

	/**
	 * @param Application $objApplication
	 */
	public function __construct(Application $objApplication) {
		$this->application = $objApplication;
	}
}