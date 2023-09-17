<?php

namespace App\Core\Assets;

use App\Constracts\Assets\AssetScriptConstract;
use App\Core\ExternalAsset;
use App\Core\HookManager;
use App\Traits\AssetScriptTrait;

class JavaScript extends ExternalAsset implements AssetScriptConstract
{
    use AssetScriptTrait;

    public function renderHtml()
    {
        echo HookManager::applyFilters(
            'print_js_html',
            sprintf(
                '<script src="%1$s"></script>',
                HookManager::applyFilters("asset_js_url", $this->getUrl(), $this->id, $this)
            ),
            $this->getId(),
            $this
        );

        parent::renderHtml();
    }
}
