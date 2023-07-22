<?php

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
