<?php

namespace App\Core\Assets;

class AssetUrl
{
    protected $url;
    protected $minUrl;

    public function __construct($url, $minUrl = null)
    {
        $this->url = $url;
        if (!is_null($minUrl)) {
            $this->minUrl = $minUrl;
        }
    }

    public function getUrl($supportMinUrl = false): string
    {
        if (!$supportMinUrl || empty($this->minUrl)) {
            return $this->url;
        }
        return $this->minUrl;
    }

    public function __toString()
    {
        return $this->getUrl();
    }
}
