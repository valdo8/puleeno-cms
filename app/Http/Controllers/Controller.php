<?php

namespace App\Http\Controllers;

use App\Constracts\BackendControllerConstract;
use App\Constracts\ControllerConstract;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;

class Controller implements ControllerConstract
{
    public function getExtensionName(): string
    {
        $class_info = new ReflectionClass($this);
        if (strpos($class_info->getFileName(), get_path('extension')) !== false) {
            $extensionPath = str_replace(get_path('extension') . DIRECTORY_SEPARATOR, '', $class_info->getFileName());
            $extensionPathArr = explode(DIRECTORY_SEPARATOR, $extensionPath);
            return $extensionPathArr[0];
        }
        return false;
    }

    public function isDashboardController(): bool
    {
        return is_a($this, BackendControllerConstract::class);
    }

    public function view(
        $template,
        $data = [],
        ResponseInterface $response = null,
        $extensionName = null
    ): ResponseInterface {
        if (empty($extensionName)) {
            $extensionName = $this->getExtensionName();
        }

        if (!is_array($data)) {
            $data = [];
        }
        $data['isDashboard'] = $this->isDashboardController();

        return extensionView($extensionName, $template, $data, $response);
    }
}
