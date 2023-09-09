<?php

use App\Common\Constants;
use App\Common\Option;
use App\Core\Application;
use App\Core\HookManager;
use Puleeno\Bootstrap;

if (!function_exists('array_get')) {
    function array_get($arr, $keyStr, $defaultValue = null)
    {
        $keys = explode('.', $keyStr);
        $value = $arr;
        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return $defaultValue;
            }
            $value = $value[$key];
        }

        return $value;
    }
}

if (!function_exists('get_path')) {
    function get_path($name)
    {
        return Constants::path($name);
    }
}


if (!function_exists('get_option')) {
    function get_option($optionName, $defaultValue = null)
    {
        return Option::getInstance()->get($optionName, $defaultValue);
    }
}


if (!function_exists('add_action')) {
    function add_action($hookName, $fn, $priority = 10, $paramsQuantity = 1)
    {
        return HookManager::addAction($hookName, $fn, $priority, $paramsQuantity);
    }
}

if (!function_exists('add_filter')) {
    function add_filter($hookName, $fn, $priority = 10, $paramsQuantity = 1)
    {
        return HookManager::addFilter($hookName, $fn, $priority, $paramsQuantity);
    }
}

if (!function_exists('get_app')) {
    function get_app(): Application
    {
        return Bootstrap::getInstance()->getApp();
    }
}
