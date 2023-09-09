<?php

namespace App\Core;

use App\Constracts\AssetConstract;
use App\Constracts\AssetTypeEnum;
use App\Constracts\ExternalAssetConstract;
use App\Core\Assets\AssetOptions;
use App\Core\Assets\AssetUrl;
use App\Core\Assets\Bucket;

final class AssetManager
{
    protected static $instance;

    protected Bucket $frontendBucket;
    protected Bucket $backendBucket;


    protected function __construct()
    {
        $this->frontendBucket = new Bucket();
        $this->backendBucket = new Bucket();
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    public static function create(
        $id,
        AssetUrl $url,
        AssetTypeEnum $assetType,
        $deps = [],
        $version = null,
        AssetOptions $assetOptions = null,
        $priority = 10
    ): AssetConstract {
        $asset = Helper::createAssetByAssetType($id, $assetType);
        if ($asset instanceof ExternalAssetConstract) {
            $asset->setUrl($url);
        }
        $asset->setDeps($deps);
        $asset->setOptions($assetOptions);
        $asset->setPriority($priority);
        $asset->setVersion($version);

        return $asset;
    }

    public static function registerAsset(
        $id,
        AssetUrl $url,
        AssetTypeEnum $assetType,
        $deps = [],
        $version = null,
        AssetOptions $assetOptions = null,
        $priority = 10
    ): self {
        $instance = static::getInstance();
        $instance->getFrontendBucket()
            ->addAsset(
                static::create($id, $url, $assetType, $deps, $version, $assetOptions, $priority)
            );
        return $instance;
    }

    public static function registerBackendAsset(
        $id,
        AssetUrl $url,
        AssetTypeEnum $assetType,
        $deps = [],
        $version = null,
        AssetOptions $assetOptions = null,
        $priority = 10
    ): self {
        $instance = static::getInstance();
        $instance->getBackendBucket()
            ->addAsset(
                static::create($id, $url, $assetType, $deps, $version, $assetOptions, $priority)
            );
        return $instance;
    }

    public function getFrontendBucket(): Bucket
    {
        return $this->frontendBucket;
    }

    public function getBackendBucket(): Bucket
    {
        return $this->backendBucket;
    }

    public function printInitHeadScripts()
    {
    }

    public function printHeadAssets()
    {
    }

    public function printExecuteHeadScripts()
    {
    }

    public function printFooterInitScripts()
    {
    }

    public function printFooterAssets()
    {
    }

    public function executeFooterScripts()
    {
    }
}
