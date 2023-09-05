<?php

namespace App\Core;

use App\Constracts\HookConstract;
use App\Core\Hooks\ActionHook;
use App\Core\Hooks\FilterHook;
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

    public static function addAction($hookName, $fn, $priority = 10, $paramsQuantity = 1)
    {
        if (!is_callable($fn)) {
            throw new NotCallableException(var_export($fn, true) . " is can not callable");
        }
        $instance = static::getInstance();
        if (!isset($instance->actions[$hookName])) {
            $instance->actions[$hookName] = [];
        }

        $instance->actions[$hookName][] = ActionHook::create($fn, $priority, $paramsQuantity);
    }

    public static function addFilter($hookName, $fn, $priority = 10, $paramsQuantity = 1)
    {
        if (!is_callable($fn)) {
            throw new NotCallableException(var_export($fn, true) . " is can not callable");
        }
        $instance = static::getInstance();
        if (!isset($instance->filters[$hookName])) {
            $instance->filters[$hookName] = [];
        }

        $instance->filters[$hookName][] = FilterHook::create($fn, $priority, $paramsQuantity);
    }

    /**
     * Undocumented function
     *
     * @param \App\Constracts\HookConstract[] $hooks
     * @return void
     */
    protected function sortHookByPriority(array $hooks)
    {
        usort($hooks, function (HookConstract $hook1, HookConstract $hook2) {
            return $hook1->getPriority() - $hook2->getPriority();
        });
        return $hooks;
    }

    /**
     * @param string $hookName
     *
     * @return \App\Core\Hooks\ActionHook[]
     */
    public function getActionsByHook($hookName)
    {
        if (isset($this->actions[$hookName])) {
            return $this->sortHookByPriority($this->actions[$hookName]);
        }
        return [];
    }

    /**
     * @param string $hookName
     *
     * @return \App\Core\Hooks\FilterHook[]
     */
    public function getFiltersByHook($hookName): array
    {
        if (isset($this->filters[$hookName])) {
            return $this->sortHookByPriority($this->filters[$hookName]);
        }
        return [];
    }

    public static function executeAction($hookName, ...$params)
    {
        $instance = static::getInstance();
        $hooks = $instance->getActionsByHook($hookName);

        foreach ($hooks as $hook) {
            $args = array_splice($params, 0, $hook->getParamsQuantity());
            call_user_func_array(
                $hook->getCallable(),
                $args
            );
        }
    }

    public static function applyFilters($hookName, ...$params)
    {
        $instance = static::getInstance();
        $value = count($params) > 0 ? $params[0] : null;
        $hooks = $instance->getFiltersByHook($hookName);
        foreach ($hooks as $hook) {
            $value = call_user_func_array(
                $hook->getCallable(),
                array_splice(
                    $params,
                    0,
                    $hook->getParamsQuantity()
                )
            );
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
