<?php

use App\Constracts\AssetTypeEnum;
use App\Core\AssetManager;
use App\Core\Assets\AssetScriptOptions;
use App\Core\Assets\AssetUrl;
use App\Core\ExtensionManager;

// This is theme setup file


class GeogoeBootstrap
{
    public function boot()
    {
        $reactExt = ExtensionManager::getExtension('puleeno-cms/react');
        $reactAsset = $reactExt->getReactAsset();
        AssetManager::getInstance()->getFrontendBucket()->addAsset($reactAsset);

        AssetManager::registerAsset(
            'dashboard',
            new AssetUrl(sprintf('%s/assets/js/geogoe.js', get_active_theme_url())),
            AssetTypeEnum::JS(),
            [$reactAsset->getId()],
            '1.0.0',
            AssetScriptOptions::parseOptionFromArray([
                'is_footer' => true,
            ])
        )->enqueue();
    }
}

$bootstraper = new GeogoeBootstrap();
$bootstraper->boot();
