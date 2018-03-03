<?php
/**
 * Simple (HTML) view wrapper, used whenever an explicit wrapper for content type was not set.
 */
class ViewWrapper extends Wrapper {
	public function run() {
		$view = $this->response->getView();
		if($view) {
			$_VIEW = $this->response->toArray();
			$view .= ".php";
			if(!file_exists($view)) throw new ServletException("View file not found: ".$view);
			require_once($view);
		}
	}
}