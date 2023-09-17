<?php

namespace App\Core;

use App\Constracts\Assets\AssetConstract;
use App\Constracts\AssetTypeEnum;
use App\Constracts\Assets\AssetExternalConstract;
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
        if ($asset instanceof AssetExternalConstract) {
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
    ): AssetConstract {
        $asset = static::create(
            (string) $id,
            $url,
            $assetType,
            $deps,
            $version,
            $assetOptions,
            $priority
        );
        $instance = static::getInstance();
        $instance->getFrontendBucket()
            ->addAsset($asset);

        return $asset;
    }

    public static function registerBackendAsset(
        $id,
        AssetUrl $url,
        AssetTypeEnum $assetType,
        $deps = [],
        $version = null,
        AssetOptions $assetOptions = null,
        $priority = 10
    ): AssetConstract {
        /**
         * @var \App\Core\Assets\JavaScript
         */
        $asset = static::create($id, $url, $assetType, $deps, $version, $assetOptions, $priority);
        $instance = static::getInstance();
        $instance->getBackendBucket()
            ->addAsset($asset);

        return $asset;
    }

    public function getFrontendBucket(): Bucket
    {
        return $this->frontendBucket;
    }

    public function getBackendBucket(): Bucket
    {
        return $this->backendBucket;
    }

    protected function getActiveBucket(): Bucket
    {
        return !Helper::isDashboard()
            ? $this->getFrontendBucket()
            : $this->getBackendBucket();
    }

    public function printInitHeadScripts()
    {
        foreach ($this->getActiveBucket()->getInitScripts(false) as $initScript) {
            $initScript->printHtml();
        }
    }

    public function printHeadAssets()
    {
        foreach ($this->getActiveBucket()->getStylesheets(true) as $css) {
            $css->printHtml();
        }
        foreach ($this->getActiveBucket()->getJs(false, true) as $js) {
            $js->printHtml();
        }
    }

    public function printExecuteHeadScripts()
    {
        foreach ($this->getActiveBucket()->getExecuteScripts(false) as $executeScript) {
            $executeScript->printHtml();
        }
    }

    public function printFooterInitScripts()
    {
        foreach ($this->getActiveBucket()->getInitScripts(true) as $initScript) {
            $initScript->printHtml();
        }
    }

    public function printFooterAssets()
    {
        foreach ($this->getActiveBucket()->getJs(true, true) as $js) {
            $js->printHtml();
        }
    }

    public function executeFooterScripts()
    {
        foreach ($this->getActiveBucket()->getExecuteScripts(true) as $executeScript) {
            $executeScript->printHtml();
        }
    }
}
