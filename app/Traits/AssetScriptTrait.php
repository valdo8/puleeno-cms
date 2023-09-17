<?php

namespace App\Traits;

trait AssetScriptTrait
{
    use AssetBaseTrait;

    protected $isFooterScript = false;

    public function isFooterScript(): bool
    {
        return $this->getOption('isFooter', false);
    }
}
