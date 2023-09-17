<?php

namespace App\Constracts\Assets;

use App\Core\Assets\AssetUrl;

interface AssetExternalConstract extends AssetConstract
{
    public function setUrl(AssetUrl $url): self;

    public function getUrl();
}
