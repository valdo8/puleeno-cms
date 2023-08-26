<?php

declare(strict_types=1);

namespace App\Core\Settings;

class Settings implements SettingsInterface
{
    private array $settings;
    private array $builtInExtensions = ['layout', 'react', 'blueprint', 'text-editor', 'file-manager', 'dashboard'];

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return mixed
     */
    public function get(string $key = '', $defaultValue = null)
    {
        if (empty($key)) {
            $this->settings;
        }
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        }
        return $defaultValue;
    }

    public function isBuiltInExtension(string $extensionName)
    {
        return in_array($extensionName, $this->builtInExtensions);
    }
}
