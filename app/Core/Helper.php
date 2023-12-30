<?php

namespace App\Core;

use App\Constracts\Assets\AssetConstract;
use App\Constracts\AssetTypeEnum;
use App\Core\Assets\AssetUrl;
use App\Core\Assets\CascadingStyleSheets;
use App\Core\Assets\Font;
use App\Core\Assets\Icon;
use App\Core\Assets\JavaScript;
use App\Core\Assets\Script;
use App\Core\Assets\Style;
use App\Exceptions\ClassNotFoundException;
use App\Exceptions\InvalidAssetTypeException;
use Psr\Container\ContainerInterface;
use ReflectionClass;

final class Helper
{
    protected static $isDashboard = false;
    protected static $updateUpdatePath = null;

    public static function createExtensionAssetUrl($extensionDirectory, $path, $minPath = null): AssetUrl
    {
        $extensionDirectoryUrl = str_replace(ROOT_PATH, '', $extensionDirectory);
        if (strpos(PHP_OS, 'WINNT') !== false) {
            $extensionDirectoryUrl = str_replace('\\', '/', $extensionDirectoryUrl);
        }

        return new AssetUrl(
            implode('/', [$extensionDirectoryUrl, 'assets', $path]),
            !empty($minPath) ? implode('/', [$extensionDirectoryUrl, 'assets', $minPath]) : null
        );
    }

    public static function createAssetByAssetType($id, AssetTypeEnum $assetType): AssetConstract
    {
        switch ($assetType->getType()) {
            case AssetTypeEnum::CSS():
                return (new CascadingStyleSheets($id))
                    ->setAssetType($assetType);
            case AssetTypeEnum::FONT():
                return (new Font($id))
                    ->setAssetType($assetType);
            case AssetTypeEnum::ICON():
                return (new Icon($id))
                    ->setAssetType($assetType);
            case AssetTypeEnum::JS():
                return (new JavaScript($id))
                    ->setAssetType($assetType);
            case AssetTypeEnum::INIT_SCRIPT():
                return (new Script($id))
                    ->setAssetType($assetType);
            case AssetTypeEnum::STYLE():
                return (new Style($id))
                    ->setAssetType($assetType);
        }
        throw new InvalidAssetTypeException($assetType);
    }


    /**
     * @param \ReflectionProperty[] $objectProperties
     * @return array
     */
    protected static function extractPropertyNames($objectProperties): array
    {
        $properties = [];
        foreach ($objectProperties as $objectProperty) {
            array_push($properties, $objectProperty->getName());
        }
        return $properties;
    }

    protected static function filterInvalidProperties($valueKeys, $propertyNames): array
    {
        $invalueProperties = [];
        foreach ($valueKeys as $valueKey) {
            if (!in_array($valueKey, $propertyNames)) {
                array_push($invalueProperties, $valueKey);
            }
        }
        return $invalueProperties;
    }

    protected static function convertKeyToCamel($key)
    {
        return preg_replace_callback('/_(\w)/', function ($matches) {
            return strtoupper($matches[1]);
        }, $key);
    }

    public static function convertArrayValuesToObject($rawValue, $className = null)
    {
        if (is_null($className) || !is_array($rawValue)) {
            return $rawValue;
        }
        if (!class_exists($className, true)) {
            throw new ClassNotFoundException($className);
        }
        $reflectClass = new ReflectionClass($className);
        $object = $reflectClass->newInstance();
        $properties = static::extractPropertyNames($reflectClass->getProperties());
        $inValidProperies = static::filterInvalidProperties(array_keys($rawValue), $properties);

        foreach ($rawValue as $rawKey => $value) {
            $keys = array_unique([$rawKey, self::convertKeyToCamel($rawKey)]);
            foreach ($keys as $key) {
                if (in_array($key, $inValidProperies)) {
                    continue;
                }
                $property = $reflectClass->getProperty($key);
                $property->setAccessible(true);
                $propertyType = $property->getType();
                if (is_array($value) && !empty($propertyType) && class_exists($propertyType->getName())) {
                    $property->setValue(
                        $object,
                        static::convertArrayValuesToObject($value, $propertyType->getName())
                    );
                } else {
                    $property->setValue($object, $value);
                }
                $property->setAccessible(false);
                break;
            }
        }

        return $object;
    }

    public static function getContainer(): ContainerInterface
    {
        return Application::getInstance()->getContainer();
    }

    public static function isDashboard()
    {
        return self::$isDashboard;
    }

    protected static function convertArrayToString($arr, $joinCharacter = ',')
    {
        $ret = '';
        if (is_array($arr)) {
            foreach ($arr as $index => $value) {
                if (is_array($value)) {
                    $ret .= self::convertArrayToString($value, $joinCharacter);
                } else {
                    $ret .= ($index > 0 ? ' ' : '') . $value;
                }
            }
            return $ret;
        }
        return $arr;
    }

    public static function generateHtmlAttributes($attributes, $skipEmpty = false)
    {
        $ret = '';
        foreach ($attributes as $attributeName => $attributeValue) {
            if (empty($attributeValue)) {
                if (!empty($attributeName) && !$skipEmpty) {
                    $ret = $attributeName;
                }
                continue;
            }
            $ret .= sprintf('%s="%s"', $attributeName, static::convertArrayToString($attributeValue, ' '));
        }

        return $ret;
    }
}
