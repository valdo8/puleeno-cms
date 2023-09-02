<?php

use App\Common\Constants;
use App\Common\Option;

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
