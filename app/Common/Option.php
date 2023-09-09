<?php

namespace App\Common;

use App\Core\Helper;
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



    public function get($name, $defaultValue = null, $mapToObjectClass = null)
    {
        $value = property_exists($this, $name) ? $this->$name : $defaultValue;

        if (is_null($mapToObjectClass)) {
            return $value;
        }
        return Helper::convertArrayValuesToObject($value, $mapToObjectClass);
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
