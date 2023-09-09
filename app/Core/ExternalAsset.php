<?php

namespace App\Core;

use App\Constracts\ExternalAssetConstract;
use App\Core\Assets\AssetUrl;

abstract class ExternalAsset extends Asset implements ExternalAssetConstract
{
    protected AssetUrl $url;

    public function setUrl(AssetUrl $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl($supportMinUrl = false)
    {
        if (empty($this->url)) {
            return "";
        }

        return $this->url->getUrl($supportMinUrl);
    }
}
