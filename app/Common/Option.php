<?php

namespace App\Common;

use App\Exceptions\ClassNotFoundException;
use ReflectionClass;

final class Option
{
    protected static $instance;

    protected function __construct()
    {
        // default theme
        $this->set('theme', 'default');
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __get($name)
    {
        return $this->get($name, null, null);
    }

    public function set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * @param \ReflectionProperty[] $objectProperties
     * @return array
     */
    protected function extractPropertyNames($objectProperties): array
    {
        $properties = [];
        foreach ($objectProperties as $objectProperty) {
            array_push($properties, $objectProperty->getName());
        }
        return $properties;
    }

    protected function filterInvalidProperties($valueKeys, $propertyNames): array
    {
        $invalueProperties = [];
        foreach ($valueKeys as $valueKey) {
            if (!in_array($valueKey, $propertyNames)) {
                array_push($invalueProperties, $valueKey);
            }
        }
        return $invalueProperties;
    }

    protected function mapOptionValueToObject($rawValue, $className = null)
    {
        if (is_null($className) || !is_array($rawValue)) {
            return $rawValue;
        }
        if (!class_exists($className, true)) {
            throw new ClassNotFoundException($className);
        }
        $reflectClass = new ReflectionClass($className);
        $object = $reflectClass->newInstance();
        $properties = $this->extractPropertyNames($reflectClass->getProperties());
        $validProperies = $this->filterInvalidProperties(array_keys($rawValue), $properties);

        foreach ($rawValue as $key => $value) {
            if (!in_array($key, $validProperies)) {
                $property = $reflectClass->getProperty($key);
                $property->setAccessible(true);
                $propertyType = $property->getType();
                if (is_array($value) && !empty($propertyType) && class_exists($propertyType->getName())) {
                    $property->setValue(
                        $object,
                        $this->mapOptionValueToObject($value, $propertyType->getName())
                    );
                } else {
                    $property->setValue($object, $value);
                }
                $property->setAccessible(false);
            }
        }

        return $object;
    }

    public function get($name, $defaultValue = null, $mapToObjectClass = null)
    {
        $value = property_exists($this, $name) ? $this->$name : $defaultValue;

        if (is_null($mapToObjectClass)) {
            return $value;
        }
        return $this->mapOptionValueToObject($value, $mapToObjectClass);
    }

    public static function __callStatic($name, $arguments)
    {
        $args = array_slice($arguments, 0, 2, false) + [0 => null, 1 => null];

        return static::getOption(
            $name,
            $args[0],
            $args[1]
        );
    }

    public static function getOption($name, $defaultValue = null, $mapToObjectClass = null)
    {
        $instance = static::getInstance();

        return $instance->get(
            $name,
            $defaultValue,
            $mapToObjectClass
        );
    }
}
