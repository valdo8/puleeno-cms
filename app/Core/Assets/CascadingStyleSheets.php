<?php

namespace App\Core\Assets;

use App\Core\ExternalAsset;
use App\Core\HookManager;

class CascadingStyleSheets extends ExternalAsset
{
    public function renderHtml()
    {
        echo HookManager::applyFilters(
            'print_css_html',
            sprintf(
                '<link rel="stylesheet" href="%1$s" type="text/css" />',
                HookManager::applyFilters("asset_css_url", $this->getUrl(), $this->id, $this)
            ),
            $this->getId(),
            $this
        );

        parent::renderHtml();
    }
}
