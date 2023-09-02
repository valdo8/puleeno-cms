<?php

namespace App\Core;

use App\Constracts\ExtensionConstract;
use App\Core\Extension\ExtensionInfo;
use App\Core\Extension\Resolver as ExtensionResolver;
use RuntimeException;

/**
 * @method static self registerModule(string $moduleName, \App\Constracts\ExtensionConstract $extension)
 * @method static \App\Constracts\ExtensionConstract getModule(string $extensionName)
 * @method static boolean hasExtension($extensionName)
 * @method static void loadExtensions()
 */
class ExtensionManager
{
    protected $registerModule;
    protected $getModule;
    protected $hasExtension;


    protected $extensions = [];

    protected $builtInPriority = [
        0 => 'admin',
    ];

    protected $activeExtensions = [];

    protected static $instance;

    protected function __construct()
    {
        $this->registerModule = function (string $moduleName, ExtensionConstract $extension): self {
            $this->extensions[$moduleName] = $extension;

            return $this;
        };

        $this->getModule = function ($moduleName): ExtensionConstract {
            if (isset($this->extensions[$moduleName])) {
                return $this->extensions[$moduleName];
            }
            throw new \Exception("Module {$moduleName} is not registered.");
        };


        $this->hasExtension = function ($extension): bool {
            return isset($this->extensions[$extension]);
        };
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
        $callable = $instance->$name;

        if (!property_exists($instance, $name) || !is_callable($callable)) {
            throw new RuntimeException(sprintf("The method %s::%s() is not defined", __CLASS__, $name));
        }
        return call_user_func_array($callable, $arguments);
    }

    /**
     * Get all extensions
     *
     * @return \App\Core\Extension\ExtensionInfo[]
     */
    public static function getAllExtensions(): array
    {
        $extensions = [];
        $extensionFiles = glob(EXTENSIONS_DIR . DIRECTORY_SEPARATOR . '{*/*,*}.json', GLOB_BRACE);

        foreach ($extensionFiles as $extensionFile) {
            $jsonStr       = file_exists($extensionFile) ? file_get_contents($extensionFile) : '';
            $json          = json_decode($jsonStr, true);
            $extensionInfo = static::parseExtensionInfo($json);

            $extensionInfo->setRootDir(dirname($extensionFile));
            $extensionInfo->loadVendor();

            if ($extensionInfo->isValid()) {
                $extensions[$extensionInfo->getExtensionName()] = $extensionInfo;
            }
        }

        return $extensions;
    }

    /**
     * Parse extension info from composer.json
     *
     * @return ExtensionInfo
     */
    protected static function parseExtensionInfo($json): ExtensionInfo
    {
        $extInfo = new ExtensionInfo();

        if (isset($json['extension-class'])) {
            $extInfo->setExtensionClass($json['extension-class']);
        }
        if (isset($json['name'])) {
            $extInfo->setExtensionName($json['name']);
        }
        if (isset($json['description'])) {
            $extInfo->setDescription($json['description']);
        }
        if (isset($json['version'])) {
            $extInfo->setVersion($json['version']);
        }

        $extInfo->setVendorDirectory(array_get($json, 'config.vendor-dir', 'vendor'));
        $extInfo->setDeps(array_get($json, 'require-extensions', []));

        return $extInfo;
    }

    public function addActiveExtension(ExtensionConstract $extension)
    {
        array_push($this->activeExtensions, $extension);
    }

    public function loadExtensions(&$app, &$container)
    {
        $instance = static::getInstance();
        $extensionResolver = new ExtensionResolver($app, $container);

        foreach ($extensionResolver->resolve() as $extension) {
            $instance->addActiveExtension($extension);

            // Call the bootstrap
            $extension->bootstrap();

            $extension->registerRoutes();

            $callable = $extension->getResponeCallback();
            if (!is_null($callable)) {
                HookManager::addFilter('response', $callable);
            }
        }
    }

    /**
     * @return \App\Constracts\ExtensionConstract[]
     */
    public function getActiveExtensions()
    {
        if (is_null($this->activeExtensions)) {
            return [];
        }
        return $this->activeExtensions;
    }

    /**
     * Run active extensions
     *
     * @return void
     */
    public function runActiveExtensions()
    {
        foreach ($this->getActiveExtensions() as $extension) {
            $extension->run();
        }
    }
}
