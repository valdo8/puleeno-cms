<?php

declare(strict_types=1);

namespace App\Core\Settings;

class Settings implements SettingsInterface
{
    private array $settings;
    private array $builtInExtensions = ['layout', 'react', 'blueprint', 'text-editor', 'file-manager', 'dashboard'];

    public function __construct(array $settings)
    {
        $this->settings = $this->init($settings);
    }

    private function init($settings, $prefix = null, &$ret = []): array
    {
        // Reset data type
        if (!is_array($ret)) {
            $ret = [];
        }

        foreach ($settings as $key => $subSettings) {
            $settingKey = empty($prefix) ? $key : sprintf('%s.%s', $prefix, $key);
            if (is_array($subSettings)) {
                $this->init($subSettings, $settingKey, $ret);
            } else {
                $ret[$settingKey] = $subSettings;
            }
        }

        return $ret;
    }

    /**
     * @return mixed
     */
    public function get(string $key = '', $defaultValue = null)
    {
        if (empty($key)) {
            return $this->settings;
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
