<?php

use App\Common\Constants;
use App\Common\Option;
use App\Core\HookManager;

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

if (!function_exists('getPath')) {
    function getPath($name)
    {
        return Constants::path($name);
    }
}


if (!function_exists('getOption')) {
    function getOption($optionName, $defaultValue = null)
    {
        return Option::getInstance()->get($optionName, $defaultValue);
    }
}

if (!function_exists('extractExtensionNameFromFilePath')) {
    function extractExtensionNameFromFilePath($path)
    {
    }
}


if (!function_exists('add_action')) {
    function add_action($hookName, $fn)
    {
        return HookManager::addAction($hookName, $fn);
    }
}

if (!function_exists('add_filter')) {
    function add_filter($hookName, $fn)
    {
        return HookManager::addFilter($hookName, $fn);
    }
}
