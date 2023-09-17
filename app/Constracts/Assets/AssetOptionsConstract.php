<?php

namespace App\Constracts\Assets;

interface AssetOptionsConstract
{
    public static function parseOptionFromArray($options): AssetOptionsConstract;
}
