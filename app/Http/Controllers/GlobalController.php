<?php

namespace App\Http\Controllers;

use App\Core\HookManager;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GlobalController extends Controller
{
    public function handle(ResponseInterface $response, RequestInterface $request)
    {
        HookManager::getInstance()->executeAction(
            'global_controller_action',
            $request,
            $response
        );

        return HookManager::getInstance()->applyFilters(
            'global_controller_response',
            $response,
            $request
        );
    }
}
