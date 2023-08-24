<?php

namespace App\Core\Extension;

use App\Constracts\ExtensionConstract;

class ExtensionInfo
{
    protected $name;
    protected $description;
    protected $version;

    protected $rootDir;

    protected $extensionClass;

    protected $vendorDirectory;

    protected $deps = [];

    public function setExtensionName($name)
    {
        return $this->name = trim($name);
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getExtensionName()
    {
        return $this->name;
    }

    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function setVendorDirectory($vendorDirectory)
    {
        $this->vendorDirectory = $vendorDirectory;
    }

    public function setExtensionClass($extensionClass)
    {
        $this->extensionClass = $extensionClass;
    }

    public function getExtension(): ?ExtensionConstract
    {
        if ($this->isValid()) {
            /**
             * @var \App\Constracts\ExtensionConstract
             */
            $extension = new $this->extensionClass();
            $extension->setExtensionDir($this->rootDir);
            $extension->setExtensionName($this->getExtensionName());

            return $extension;
        }
        return null;
    }

    public function isValid(): bool
    {
        return !empty($this->extensionClass) && class_exists($this->extensionClass, true);
    }

    public function loadVendor()
    {
        $autoloader = implode(DIRECTORY_SEPARATOR, [$this->rootDir, $this->vendorDirectory, 'autoload.php']);
        if (file_exists($autoloader)) {
            require_once $autoloader;
        }
    }

    public function setDeps(array $deps = null)
    {
        if (is_array($deps)) {
            $this->deps = $deps;
        }
    }

    /**
     * @return array
     */
    public function getDeps(): array
    {
        return $this->deps;
    }
}
