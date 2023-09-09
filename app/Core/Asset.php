<?php

namespace App\Core;

use App\Constracts\AssetConstract;
use App\Constracts\AssetTypeEnum;
use App\Core\Assets\AssetOptions;

abstract class Asset implements AssetConstract
{
    protected $id;

    protected AssetTypeEnum $assetType;
    protected AssetOptions $options;

    protected $deps     = [];
    protected $version  = null;
    protected $priority = 10;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function isValid(): bool
    {
        return !empty($this->id);
    }

    public function setAssetType(AssetTypeEnum $assetType): AssetConstract
    {
        $this->assetType = $assetType;

        return $this;
    }

    public function getAssetType(): AssetTypeEnum
    {
        return $this->assetType;
    }

    public function setDeps($deps): AssetConstract
    {
        if (is_array($deps)) {
            $this->deps = $deps;
        }
        return $this;
    }

    public function setOptions(AssetOptions $assetOptions): AssetConstract
    {
        $this->options = $assetOptions;

        return $this;
    }

    public function setPriority(int $priority): AssetConstract
    {
        $this->priority = $priority;

        return $this;
    }

    public function setVersion($version): AssetConstract
    {
        $this->version = $version;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }
}
