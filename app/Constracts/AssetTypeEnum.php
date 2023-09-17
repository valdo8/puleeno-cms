<?php

namespace App\Constracts;

final class AssetTypeEnum
{
    private static $inited = false;

    protected static AssetTypeEnum $CSS;
    protected static AssetTypeEnum $JS;
    protected static AssetTypeEnum $ICON;
    protected static AssetTypeEnum $FONT;
    protected static AssetTypeEnum $STYLE;
    protected static AssetTypeEnum $INIT_SCRIPT;
    protected static AssetTypeEnum $EXECUTE_SCRIPT;

    protected $type;

    protected function __construct($type)
    {
        $this->type = $type;
    }

    public static function init()
    {
        if (static::$inited === false) {
            self::$CSS = new self('css');
            self::$JS = new self('js');
            self::$ICON = new self('icon');
            self::$FONT = new self('font');
            self::$STYLE = new self('style');

            // phpcs:ignoreFile
            self::$INIT_SCRIPT = new self('init_script');
            // phpcs:ignoreFile
            self::$EXECUTE_SCRIPT = new self('execute_script');

            // Make inited flag is true
            static::$inited = true;
        }
    }

    public static function CSS(): AssetTypeEnum
    {
        return static::$CSS;
    }

    public static function JS(): AssetTypeEnum
    {
        return static::$JS;
    }

    public static function ICON(): AssetTypeEnum
    {
        return static::$ICON;
    }

    public static function FONT(): AssetTypeEnum
    {
        return static::$FONT;
    }

    public static function STYLE(): AssetTypeEnum
    {
        return static::$STYLE;
    }

    public static function INIT_SCRIPT(): AssetTypeEnum
    {
        return static::$INIT_SCRIPT;
    }

    public static function EXECUTE_SCRIPT(): AssetTypeEnum
    {
        return static::$EXECUTE_SCRIPT;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function __toString()
    {
        return $this->getType();
    }
}
