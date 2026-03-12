<?php

namespace Lucinda\STDOUT\Service;

use Lucinda\STDOUT\Validators\ValidatedRequest;
use Lucinda\MVC\Response\View;
use Lucinda\MVC\ConfigurationException;
use Lucinda\MVC\Application;

final class ViewDetector
{
    private View $view;
    
    public function __construct(
        Application $application,
        ValidatedRequest $validatedRequest,
        ?View $filledView
    )
    {
        $this->view = new View(
            $filledView?->getData() ?? [],
            $this->getTemplate($application, $validatedRequest, $filledView)
        );
    }

    private function getTemplate(
        Application $application,
        ValidatedRequest $validatedRequest,
        ?View $filledView
    ): ?string
    {
        $info = $application->getApplicationInfo();
        $viewsFolder = $info->getViewsFolder();
        $viewsExtension = $info->getViewsExtension();
        $template = $filledView?->getFile()?:$application->getRoutes($validatedRequest->getPage())->getView();

        $fullViewPath = null;
        if (!empty($template)) {
            if (empty($viewsFolder) || empty($viewsExtension)) {
                throw new ConfigurationException(
                    "If views are used, setting 'views_folder' attribute in 'application' tag is required"
                );
            }
            $fullViewPath = $viewsFolder . DIRECTORY_SEPARATOR . $template . "." . $viewsExtension;
            if (!file_exists($fullViewPath)) {
                throw new ConfigurationException(
                    "View file doesn't exist: ". $fullViewPath
                );
            }
        }
        return $fullViewPath;
    }

    public function getView(): View
    {
        return $this->view;
    }
}