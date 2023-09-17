<?php

namespace App\Core\Assets;

use App\Constracts\Assets\AssetOptionsConstract;
use App\Core\Helper;

class AssetOptions implements AssetOptionsConstract
{
    public static function parseOptionFromArray($options): AssetOptionsConstract
    {
        return Helper::convertArrayValuesToObject(
            $options,
            static::class
        );
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    public function __call($name, $arguments)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return array_get($arguments, 0, null);
    }
}
