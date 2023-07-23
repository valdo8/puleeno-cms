<?php

namespace App\Core;

use RuntimeException;

class HookManager
{
    protected $actions = [];
    protected $filters = [];

    protected static $instance;

    protected function __construct()
    {
        //
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    public function __callStatic($name, $arguments)
    {
        $instance = static::getInstance();
        if (!method_exists($instance, $name)) {
            throw new RuntimeException("The method " . __CLASS__ . '::' . $name . ' is not defined');
        }
        return call_user_func_array([$instance, $name], $arguments);
    }

    public function addAction($hookName, callable $fn)
    {
        if (!isset($this->actions[$hookName])) {
            $this->actions[$hookName] = [];
        }

        $this->actions[$hookName][] = $fn;
    }

    public function addFilter($hookName, callable $fn)
    {
        if (!isset($this->filters[$hookName])) {
            $this->filters[$hookName] = [];
        }

        $this->filters[$hookName][] = $fn;
    }

    public function executeAction($hookName, ...$params)
    {
        if (isset($this->actions[$hookName])) {
            foreach ($this->actions[$hookName] as $fn) {
                call_user_func_array($fn, $params);
            }
        }
    }

    public function applyFilter($hookName, $value, $applyFirstHook = false)
    {
        if (isset($this->filters[$hookName])) {
            foreach ($this->filters[$hookName] as $fn) {
                $value = call_user_func($fn, $value);
                if ($applyFirstHook) {
                    return $value;
                }
            }
        }

        return $value;
    }
}
