<?php

namespace App\Common;

final class Constants
{
    protected static $paths = [
        'root' => ROOT_PATH,
        'theme' => THEMES_DIR,
        'config' => CONFIGS_DIR,
        'resoure' => RESOURCES_DIR,
        'storage' => STORAGES_DIR,
        'extension' => EXTENSIONS_DIR,
        'cache' => STORAGES_DIR . DIRECTORY_SEPARATOR . 'caches'
    ];

    public static function path($name)
    {
        return array_get(static::$paths, $name);
    }
}
