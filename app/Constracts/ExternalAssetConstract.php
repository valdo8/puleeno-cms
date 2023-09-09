<?php

namespace App\Constracts;

use App\Core\Assets\AssetUrl;

interface ExternalAssetConstract extends AssetConstract
{
    public function setUrl(AssetUrl $url): self;

    public function getUrl();
}
