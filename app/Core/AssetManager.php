<?php

namespace App\Core;

use App\Constracts\Assets\AssetConstract;
use App\Constracts\AssetTypeEnum;
use App\Constracts\Assets\AssetExternalConstract;
use App\Constracts\Assets\AssetScriptConstract;
use App\Core\Assets\AssetOptions;
use App\Core\Assets\AssetUrl;
use App\Core\Assets\Bucket;
use MJS\TopSort\Implementations\StringSort;

final class AssetManager
{
    protected static $instance;

    protected Bucket $frontendBucket;
    protected Bucket $backendBucket;

    protected $resolvedAssets = [];


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
        AssetOptions $assetOptions = null
    ): AssetConstract {
        $asset = Helper::createAssetByAssetType($id, $assetType);
        if ($asset instanceof AssetExternalConstract) {
            $asset->setUrl($url);
        }
        $asset->setDeps($deps);
        $asset->setOptions($assetOptions);
        $asset->setVersion($version);

        return $asset;
    }

    public static function registerAsset(
        $id,
        AssetUrl $url,
        AssetTypeEnum $assetType,
        $deps = [],
        $version = null,
        AssetOptions $assetOptions = null
    ): AssetConstract {
        $asset = static::create(
            (string) $id,
            $url,
            $assetType,
            $deps,
            $version,
            $assetOptions
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
        AssetOptions $assetOptions = null
    ): AssetConstract {
        /**
         * @var \App\Core\Assets\JavaScript
         */
        $asset = static::create($id, $url, $assetType, $deps, $version, $assetOptions);
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

    protected function getAssetTypeFromStringType($type): ?AssetTypeEnum
    {
        switch ($type) {
            case AssetTypeEnum::CSS()->getType():
                return AssetTypeEnum::CSS();
            case AssetTypeEnum::JS()->getType():
                return AssetTypeEnum::JS();
            case AssetTypeEnum::FONT()->getType():
                return AssetTypeEnum::FONT();
            case AssetTypeEnum::ICON()->getType():
                return AssetTypeEnum::ICON();
            case AssetTypeEnum::STYLE()->getType():
                return AssetTypeEnum::STYLE();
            case AssetTypeEnum::EXECUTE_SCRIPT()->getType():
                return AssetTypeEnum::EXECUTE_SCRIPT();
        }
    }

    protected function resolve(AssetConstract $asset, StringSort &$resolver)
    {
        $bucket = $this->getActiveBucket();
        $resolver->add($asset->getId(), $asset->getDeps());
        foreach ($asset->getDeps() as $assetId) {
            $assetDep = $bucket->getAsset($assetId, $asset->getAssetType());
            if (is_null($assetDep)) {
                error_log('Asset ID #' . $assetId . ' is not exists');
                continue;
            }
            $assetDep->enqueue();

            $this->resolve($assetDep, $resolver);
        }
    }

    /**
     * @param \App\Constracts\Assets\AssetConstract[] $assets
     * @param AssetTypeEnum $assetType
     * @return void
     */
    protected function resolveDependences($assets, AssetTypeEnum $assetType)
    {
        $bucket = $this->getActiveBucket();
        $resolver      = new StringSort();

        foreach ($assets as $asset) {
            $this->resolve($asset, $resolver);
        }
        $sortedAssets = $resolver->sort();
        foreach ($sortedAssets as $assetId) {
            $asset = $bucket->getAsset($assetId, $assetType);
            if (is_null($asset)) {
                error_log('Asset ID #' . $assetId . ' is not exists');
                continue;
            }
            if (!isset($this->resolvedAssets[$assetType->getType()])) {
                $this->resolvedAssets[$assetType->getType()] = [];
            }
            $this->resolvedAssets[$assetType->getType()][] = $asset;
        }
    }

    public function resolveAllDependences()
    {
        $bucket = $this->getActiveBucket();
        foreach ($bucket->getAssets() as $type => $assets) {
            $assetType = $this->getAssetTypeFromStringType($type);
            if (is_null($assetType)) {
                continue;
            }
            $this->resolveDependences(array_filter($assets, function (AssetConstract $item) {
                return $item->isEnqueue() === true;
            }), $assetType);
        }
    }

    protected function getEnqueueAssetsByType(AssetTypeEnum $assetType, $filter = null)
    {
        if (!isset($this->resolvedAssets[$assetType->getType()])) {
            return [];
        }

        return is_null($filter)
            ? $this->resolvedAssets[$assetType->getType()]
            : array_filter($this->resolvedAssets[$assetType->getType()], $filter);
    }

    public function printInitHeadScripts()
    {
        $headInitScripts = $this->getEnqueueAssetsByType(
            AssetTypeEnum::INIT_SCRIPT(),
            function (AssetScriptConstract $item) {
                return $item->isFooterScript() === false;
            }
        );

        foreach ($headInitScripts as $initScript) {
            if ($initScript->isRendered()) {
                continue;
            }
            $initScript->renderTabCharacter(2);
            $initScript->renderHtml();
        }
    }

    public function printHeadAssets()
    {
        foreach ($this->getEnqueueAssetsByType(AssetTypeEnum::CSS()) as $css) {
            if ($css->isRendered()) {
                continue;
            }
            $css->renderTabCharacter(2);
            $css->renderHtml();
        }

        foreach ($this->getActiveBucket()->getJs(false, true) as $js) {
            if ($js->isRendered()) {
                continue;
            }
            $js->renderTabCharacter(2);
            $js->renderHtml();
        }
    }

    public function printExecuteHeadScripts()
    {
        foreach ($this->getEnqueueAssetsByType(AssetTypeEnum::STYLE()) as $interalStyle) {
            if ($interalStyle->isRendered()) {
                continue;
            }
            $interalStyle->renderTabCharacter(2);
            $interalStyle->renderHtml();
        }
        $headExecScripts = $this->getEnqueueAssetsByType(
            AssetTypeEnum::EXECUTE_SCRIPT(),
            function (AssetScriptConstract $item) {
                return $item->isFooterScript() === false;
            }
        );

        foreach ($headExecScripts as $executeScript) {
            if ($executeScript->isRendered()) {
                continue;
            }
            $executeScript->renderTabCharacter(2);
            $executeScript->renderHtml();
        }
    }

    public function printFooterInitScripts()
    {
        $footerInitScripts = $this->getEnqueueAssetsByType(
            AssetTypeEnum::INIT_SCRIPT(),
            function (AssetScriptConstract $item) {
                return $item->isFooterScript() === true;
            }
        );

        foreach ($footerInitScripts as $initScript) {
            if ($initScript->isRendered()) {
                continue;
            }
            $initScript->renderTabCharacter(2);
            $initScript->renderHtml();
        }
    }

    public function printFooterAssets()
    {
        foreach ($this->getActiveBucket()->getJs(true, true) as $js) {
            if ($js->isRendered()) {
                continue;
            }
            $js->renderTabCharacter(2);
            $js->renderHtml();
        }
    }

    public function executeFooterScripts()
    {
        $footerExecScripts = $this->getEnqueueAssetsByType(
            AssetTypeEnum::INIT_SCRIPT(),
            function (AssetScriptConstract $item) {
                return $item->isFooterScript() === true;
            }
        );

        foreach ($footerExecScripts as $executeScript) {
            if ($executeScript->isRendered()) {
                continue;
            }
            $executeScript->renderTabCharacter(2);
            $executeScript->renderHtml();
        }
    }
}
