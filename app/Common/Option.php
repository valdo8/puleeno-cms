<?php

namespace App\Common;

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
        return $this->get($name, null);
    }

    public function set($name, $value)
    {
        $this->$name = $value;
    }

    public function get($name, $defaultValue = null)
    {
        if (!property_exists($this, $name)) {
            return $defaultValue;
        }
        return $this->$name;
    }
}
