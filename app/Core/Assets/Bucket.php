<?php

namespace App\Core\Assets;

use App\Constracts\AssetConstract;

class Bucket
{
    /**
     *
     * @var \App\Constracts\AssetConstract[]
     */
    protected $assets = [];

    protected $handleIds = [];

    public function addAsset(AssetConstract &$asset)
    {
        if (!$asset->isValid()) {
            return;
        }
        $this->assets[$asset->getAssetType()->getType()][$asset->getId()] = $asset;
    }
}
