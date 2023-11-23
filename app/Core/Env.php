<?php

namespace App\Core;

final class Env
{
    public static function get($key, $defaultValue = null)
    {
        $envVar = getenv($key);
        if (is_null($envVar)) {
            return $defaultValue;
        }

        if (in_array(($lowerValue = strtolower($envVar)), ["true", "false"])) {
            return trim($lowerValue) === "true";
        }
        if (is_numeric($envVar)) {
            if (strpos($envVar, ',') !== false) {
                $envVar = str_replace(',', '', $envVar);
            }

            return strpos($envVar, '.') === false ? intval($envVar) : floatval($envVar);
        }
        return $envVar;
    }
}
