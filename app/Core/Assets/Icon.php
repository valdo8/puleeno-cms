<?php

namespace App\Core\Assets;

use App\Constracts\Assets\AssetIconConstract;
use App\Core\Asset;

class Icon extends Asset implements AssetIconConstract
{
    public function renderHtml()
    {
        parent::renderHtml();
    }
}
