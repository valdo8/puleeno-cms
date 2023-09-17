<?php

namespace App\Core;

use App\Constracts\Assets\AssetExternalConstract;
use App\Core\Assets\AssetUrl;

abstract class ExternalAsset extends Asset implements AssetExternalConstract
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
