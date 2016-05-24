<?php
/**
 * Listens on configuration object and appends attributes to it.
 */
abstract class ApplicationListener implements Runnable {
	protected $objApplication;

	/**
	 * @param Application $objApplication
	 */
	public function __construct(Application $objApplication) {
		$this->objApplication = $objApplication;
	}
}