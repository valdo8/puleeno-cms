<?php

namespace App\Constracts;

use Psr\Http\Message\ResponseInterface;

interface ControllerConstract
{
    public function getExtensionName(): string;

    public function view(
        $template,
        $data = [],
        $extensionName = null,
        ResponseInterface $response = null
    ): ResponseInterface;
}
