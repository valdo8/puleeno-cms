<?php

namespace App\Exceptions;

use App\Constracts\AssetTypeEnum;
use Exception;

class InvalidAssetTypeException extends Exception
{
    public function __construct(AssetTypeEnum $assetTypeEnum, $code = 0)
    {
        $message = "";
        if (is_null($assetTypeEnum)) {
            $message = 'The asset type is null';
        } else {
            $message = 'The asset type value `' + $assetTypeEnum->getType() + '` is invalid';
        }
        parent::__construct($message, $code);
    }
}
