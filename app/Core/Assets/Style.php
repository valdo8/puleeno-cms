<?php

namespace App\Core\Assets;

use App\Constracts\Assets\AssetHtmlConstract;
use App\Core\Asset;

class Style extends Asset implements AssetHtmlConstract
{
    public function renderHtml()
    {
        parent::renderHtml();
    }
}
