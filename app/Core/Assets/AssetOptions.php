<?php

namespace App\Core\Assets;

use App\Core\Helper;

class AssetOptions
{
    public static function parseOptionFromArray($options): AssetOptions
    {
        return Helper::convertArrayValuesToObject(
            $options,
            AssetOptions::class
        );
    }
}
