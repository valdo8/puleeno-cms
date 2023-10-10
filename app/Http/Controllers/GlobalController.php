<?php

namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface;

class GlobalController extends Controller
{
    public function handle(ResponseInterface $response)
    {
        return $response;
    }
}
