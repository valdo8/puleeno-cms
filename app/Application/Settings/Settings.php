<?php

declare(strict_types=1);

namespace App\Application\Settings;

class Settings implements SettingsInterface
{
    private array $settings;
    private array $builtInExtensions = ['admin', 'react', 'master-layout'];

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return mixed
     */
    public function get(string $key = '')
    {
        return (empty($key)) ? $this->settings : $this->settings[$key];
    }

    public function isBuiltInExtension(string $extensionName)
    {
        return in_array($extensionName, $this->builtInExtensions);
    }
}
