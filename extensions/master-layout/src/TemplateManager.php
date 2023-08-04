<?php

namespace Puleeno\Layout;

use Slim\Views\Twig;

final class TemplateManager
{
    protected Twig $twig;

    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance()
    {
    }
}
