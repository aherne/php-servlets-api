<?php
/**
 * Simple (HTML) view wrapper, used whenever an explicit wrapper for content type was not set.
 */
class ViewWrapper extends Wrapper {
	public function run() {
		$mixView = $this->objResponse->getView();
		if($mixView) {
			$_VIEW = $this->objResponse->toArray();
			$mixView .= ".php";
			if(!file_exists($mixView)) throw new ServletException("View file not found: ".$mixView);
			require_once($mixView);
		}
	}
}