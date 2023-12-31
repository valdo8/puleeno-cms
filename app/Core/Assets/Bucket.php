<?php

namespace App\Core\Assets;

use App\Constracts\Assets\AssetConstract;
use App\Constracts\AssetTypeEnum;

class Bucket
{
    /**
     *
     * @var array
     */
    protected $assets = [];

    protected $handleIds = [];

    public function addAsset(AssetConstract &$asset): self
    {
        if (!$asset->isValid()) {
            return $this;
        }
        $this->assets[$asset->getAssetType()->getType()][$asset->getId()] = $asset;
        return $this;
    }

    public function getAssets()
    {
        return $this->assets ?? [];
    }

    public function getAsset($id, AssetTypeEnum $assetType): ?AssetConstract
    {
        if (!empty($this->assets[$assetType->getType()])) {
            $assets = &$this->assets[$assetType->getType()];
            if (isset($assets[$id])) {
                return $assets[$id];
            }
        }
        return null;
    }

    public function getStylesheets($enqueueScripts = null): array
    {
        $assets = &$this->assets;
        if (!isset($assets[AssetTypeEnum::CSS()->getType()])) {
            return [];
        }
        return is_null($enqueueScripts)
            ? $assets[AssetTypeEnum::CSS()->getType()]
            : array_filter(
                $assets[AssetTypeEnum::CSS()->getType()],
                function (CascadingStyleSheets $item) use ($enqueueScripts) {
                    return $item->isEnqueue() === $enqueueScripts;
                }
            );
    }

    /**
     * @param boolean $isFooter
     *
     * @return \App\Constracts\Assets\AssetConstract[]
     */
    public function getJs($isFooter = false, $enqueueScripts = null): array
    {
        $assets = &$this->assets;
        if (!isset($assets[AssetTypeEnum::JS()->getType()])) {
            return [];
        }

        return array_filter(
            $assets[AssetTypeEnum::JS()->getType()],
            function (JavaScript $item) use ($isFooter, $enqueueScripts) {
                if (is_null($enqueueScripts)) {
                    return $item->isFooterScript() === $isFooter;
                }
                return $item->isFooterScript() === $isFooter && $item->isEnqueue() && $enqueueScripts;
            }
        );
    }

    /**
     * @param boolean $isFooter
     *
     * @return \App\Constracts\Assets\AssetConstract[]
     */
    public function getInitScripts($isFooter = false, $enqueueScripts = null): array
    {
        $assets = &$this->assets;
        if (!isset($assets[AssetTypeEnum::INIT_SCRIPT()->getType()])) {
            return [];
        }

        return array_filter(
            $assets[AssetTypeEnum::INIT_SCRIPT()->getType()],
            function (Script $item) use ($isFooter, $enqueueScripts) {
                if (is_null($enqueueScripts)) {
                    return $item->isFooterScript() === $isFooter;
                }
                return $item->isFooterScript() === $isFooter && $item->isEnqueue() && $enqueueScripts;
            }
        );
    }

    /**
     * @param boolean $isFooter
     *
     * @return \App\Constracts\Assets\AssetConstract[]
     */
    public function getExecuteScripts($isFooter = false, $enqueueScripts = null): array
    {
        $assets = &$this->assets;
        if (!isset($assets[AssetTypeEnum::EXECUTE_SCRIPT()->getType()])) {
            return [];
        }

        return array_filter(
            $assets[AssetTypeEnum::EXECUTE_SCRIPT()->getType()],
            function (Script $item) use ($isFooter, $enqueueScripts) {
                if (is_null($enqueueScripts)) {
                    return $item->isFooterScript() === $isFooter;
                }
                return $item->isFooterScript() === $isFooter && $item->isEnqueue() && $enqueueScripts;
            }
        );
    }
}
