<?php

namespace App\Http\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class StaticFileController
{
    protected function getContentTypeFromFileName($fileName)
    {
        $fileNameArr = explode('.', $fileName);
        $extension = end($fileNameArr);

        switch ($extension) {
            case 'js':
                return 'text/javascript';
            case 'css':
                return 'text/css';
            case 'json':
                return 'application/json';
            case 'jsonld':
                return 'application/ld+json';
            case 'svg':
                return 'image/svg+xml';
            case 'png':
                return 'image/png';
            case 'jpg':
            case 'jpeg':
                return 'image/jpeg';
            case 'gif':
                return 'image/gif';
            case 'ico':
                return 'image/vnd.microsoft.icon';
            case 'htm':
            case 'html':
                return 'text/html';
        }

        return 'text/plain';
    }

    public function __invoke(RequestInterface $request, ResponseInterface $response, $args = [])
    {
        $pagePath = $request->getUri()->getPath();
        $assetFile = get_path('root') . str_replace('/', DIRECTORY_SEPARATOR, $pagePath);

        $response  = $response->withHeader('Content-Type', $this->getContentTypeFromFileName(basename($pagePath)));
        $response->getBody()->write(file_get_contents($assetFile));

        return $response;
    }
}
