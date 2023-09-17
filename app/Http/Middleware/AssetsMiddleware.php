<?php

namespace App\Http\Middleware;

use App\Constracts\MiddlewareConstract;
use App\Core\HookManager;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

class AssetsMiddleware implements MiddlewareConstract
{
    public function getPriority(): int
    {
        return 9999;
    }

    protected function getSiteTitle()
    {
        return HookManager::applyFilters(
            'title',
            get_option('site_name', 'Puleeno CMS')
        );
    }

    protected function getHeadAssets()
    {
        ob_start();
        HookManager::executeAction('head');
        return ob_get_clean();
    }

    protected function getOpenBodyTags()
    {
        ob_start();
        HookManager::executeAction('start_body');
        return ob_get_clean();
    }

    protected function getFooterAssets()
    {
        ob_start();
        HookManager::executeAction('footer');
        return ob_get_clean();
    }

    // Replace template tags by action hooks
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $template = (string) $response->getBody();

        $template = str_replace([
            '<!--site:title-->',
            '<!--asset:head-->',
            '<!--html:start_body-->',
            '<!--asset:footer-->'
        ], [
            $this->getSiteTitle(),
            $this->getHeadAssets(),
            $this->getOpenBodyTags(),
            $this->getFooterAssets()
        ], $template);

        $response = new Response();
        $response->getBody()->write($template);

        return $response;
    }
}
