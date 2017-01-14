<?php
/**
 * Defines a controller that does nothing more than forwarding response to a view.
 * 
 * @author aherne
 */
class ViewController extends Controller {
	public function run() {
		if(!$this->application->getViewsPath()) throw new ServletException("View path hasn't been set!");
		$this->response->setView($this->application->getViewsPath()."/".$this->request->getAttribute("page_url"));
	}
}