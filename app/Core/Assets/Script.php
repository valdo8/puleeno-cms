<?php

namespace App\Core\Assets;

use App\Constracts\Assets\AssetConstract;
use App\Constracts\Assets\AssetHtmlConstract;
use App\Constracts\Assets\AssetScriptConstract;
use App\Core\Asset;
use App\Traits\AssetScriptTrait;

class Script extends Asset implements AssetHtmlConstract, AssetScriptConstract
{
    use AssetScriptTrait;

    public function renderHtml()
    {
        parent::renderHtml();
    }
}
