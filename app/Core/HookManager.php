<?php

namespace App\Core;

use App\Exceptions\NotCallableException;
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

    public static function __callStatic($name, $arguments)
    {
        $instance = static::getInstance();
        if (!method_exists($instance, $name)) {
            throw new RuntimeException("The method " . __CLASS__ . '::' . $name . ' is not defined');
        }
        return call_user_func_array([$instance, $name], $arguments);
    }

    public static function addAction($hookName, $fn)
    {
        if (!is_callable($fn)) {
            throw new NotCallableException(var_export($fn, true) . " is can not callable");
        }
        $instance = static::getInstance();
        if (!isset($instance->actions[$hookName])) {
            $instance->actions[$hookName] = [];
        }

        $instance->actions[$hookName][] = $fn;
    }

    public static function addFilter($hookName, $fn)
    {
        if (!is_callable($fn)) {
            throw new NotCallableException(var_export($fn, true) . " is can not callable");
        }
        $instance = static::getInstance();
        if (!isset($instance->filters[$hookName])) {
            $instance->filters[$hookName] = [];
        }

        $instance->filters[$hookName][] = $fn;
    }

    public function getActionsByHook($hookName)
    {
        if (isset($this->actions[$hookName])) {
            return $this->actions[$hookName];
        }
        return [];
    }

    /**
     * @param string $hookName
     * @return callable[]
     */
    public function getFiltersByHook($hookName): array
    {
        if (isset($this->filters[$hookName])) {
            return $this->filters[$hookName];
        }
        return [];
    }

    public static function executeAction($hookName, ...$params)
    {
        $instance = static::getInstance();
        foreach ($instance->getActionsByHook($hookName) as $fn) {
            call_user_func_array($fn, $params);
        }
    }

    public static function applyFilters($hookName, ...$params)
    {
        $instance = static::getInstance();
        $value = count($params) > 0 ? $params[0] : null;
        foreach ($instance->getFiltersByHook($hookName) as $fn) {
            $value = call_user_func_array($fn, $params);
        }

        return $value;
    }

    public static function removeAllActionsByHook($hookName)
    {
        $instance = static::getInstance();
        if (isset($instance->actions[$hookName])) {
            unset($instance->actions[$hookName]);
        }
    }

    public static function removeAllFiltersByHook($hookName)
    {
        $instance = static::getInstance();
        if (isset($instance->filters[$hookName])) {
            unset($instance->filters[$hookName]);
        }
    }
}
