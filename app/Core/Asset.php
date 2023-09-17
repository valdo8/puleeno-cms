<?php

namespace App\Core;

use App\Constracts\Assets\AssetConstract;
use App\Constracts\Assets\AssetOptionsConstract;
use App\Constracts\AssetTypeEnum;
use App\Traits\AssetBaseTrait;

abstract class Asset implements AssetConstract
{
    use AssetBaseTrait;

    protected $id;

    protected AssetTypeEnum $assetType;
    protected AssetOptionsConstract $options;

    protected $deps       = [];
    protected $version    = null;
    protected $priority   = 10;
    protected $isEnqueue  = false;
    protected $isRendered = false;

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

    public function setOptions(AssetOptionsConstract $assetOptions): AssetConstract
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

    public function enqueue(): self
    {
        $this->isEnqueue = true;

        return $this;
    }

    public function isEnqueue(): bool
    {
        return $this->isEnqueue;
    }
}
