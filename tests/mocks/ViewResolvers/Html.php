<?php

namespace Test\Lucinda\STDOUT\mocks\ViewResolvers;

use Lucinda\MVC\ViewResolver;

class Html extends ViewResolver
{
    public function run(): void
    {
        $view = $this->response->view();
        if ($view->getFile()) {
            if (!file_exists($view->getFile().".html")) {
                throw new \Exception("View file not found");
            }
            ob_start();
            $_VIEW = $view->getData();
            include $view->getFile().".html";
            $output = ob_get_contents();
            ob_end_clean();
            $this->response->setBody($output);
        }
    }
}
