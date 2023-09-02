<?php

namespace App\Http\Controllers;

use App\Constracts\ControllerConstract;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;

class Controller implements ControllerConstract
{
    public function getExtensionName(): string
    {
        $class_info = new ReflectionClass($this);
        if (strpos($class_info->getFileName(), getPath('extension')) !== false) {
            $extensionPath = str_replace(getPath('extension') . '/', '', $class_info->getFileName());
            $extensionPathArr = explode(DIRECTORY_SEPARATOR, $extensionPath);
            return $extensionPathArr[0];
        }
        return false;
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
        return extensionView($extensionName, $template, $data, $response);
    }
}
