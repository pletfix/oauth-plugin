<?php

namespace Pletfix\OAuth\Services;

use Pletfix\OAuth\Services\Contracts\OAuthFactory as OAuthFactoryContract;

use InvalidArgumentException;

class OAuthFactory implements OAuthFactoryContract
{
    /**
     * Instances of OAuth.
     *
     * @var \Core\Services\Contracts\OAuth[]
     */
    private $oauth = [];

    /**
     * Name of the default provider.
     *
     * @var string
     */
    private $defaultProvider;

    /**
     * PLugin's drivers.
     *
     * @var array|null
     */
    private $pluginDrivers;

    /**
     * Create a new factory instance.
     */
    public function __construct()
    {
        $this->defaultProvider = config('oauth.default');
    }

    /**
     * @inheritdoc
     */
    public function provider($name = null)
    {
        if ($name === null) {
            $name = $this->defaultProvider;
        }

        if (isset($this->oauth[$name])) {
            return $this->oauth[$name];
        }

        $config = config('oauth.providers.' . $name);
        if ($config === null) {
            throw new InvalidArgumentException('OAuth provider "' . $name . '" is not defined.');
        }

        if (!isset($config['driver'])) {
            throw new InvalidArgumentException('OAuth driver for provider "' . $name . '" is not specified.');
        }

        $class = $config['driver'];
        if (file_exists(app_path('Services/OAuth/' .  $class . '.php'))) {
            $class = '\\App\\Services\\OAuth\\' . $class;
        }
        else if (($pluginDriver = $this->getPluginDriver($class)) !== null) {
            $class = $pluginDriver;
        }
        else {
            $class = '\\Core\\Services\\OAuth\\' . $class;
        }

        $provider = new $class($config);

        return $this->oauth[$name] = $provider;
    }

    /**
     * Get the full qualified class name of the plugin's driver.
     *
     * @param string $class
     * @return null|string
     */
    private function getPluginDriver($class)
    {
        if ($this->pluginDrivers === null) {
            $manifest = manifest_path('plugins/classes.php');
            if (file_exists($manifest)) {
                /** @noinspection PhpIncludeInspection */
                $classes = include $manifest;
                $this->pluginDrivers = isset($classes['Socialites']) ? $classes['Socialites'] : [];
            }
            else {
                $this->pluginDrivers = [];
            }
        }

        return isset($this->pluginDrivers[$class]) ? $this->pluginDrivers[$class] : null;
    }
}