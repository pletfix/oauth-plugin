<?php

namespace Pletfix\OAuth\Services;

use Pletfix\OAuth\Services\Contracts\OAuth;
use Pletfix\OAuth\Services\Contracts\OAuthFactory as OAuthFactoryContract;

use InvalidArgumentException;

class OAuthFactory implements OAuthFactoryContract
{
    /**
     * Instances of OAuth.
     *
     * @var OAuth[]
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
     * Manifest file of classes.
     *
     * @var string
     */
    private $pluginManifestOfDrivers;

    /**
     * Create a new factory instance.
     *
     * @param string|null $pluginManifestOfDrivers
     */
    public function __construct($pluginManifestOfDrivers = null)
    {
        $this->pluginManifestOfDrivers = $pluginManifestOfDrivers ?: manifest_path('plugins/drivers.php');
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
        if (file_exists(app_path('Drivers/SocialMedia/' .  $class . '.php'))) { // todo als helper create_driver($type, $name) auslagern
            $class = '\\App\\Drivers\\SocialMedia\\' . $class;
        }
        else if (($pluginDriver = $this->getPluginDriver($class)) !== null) {
            $class = $pluginDriver;
        }
        else if (file_exists(__DIR__ . '/../Drivers/' .  str_replace('\\', '/', $class) . '.php')) {
            $class = '\\Core\\Drivers\\SocialMedia\\' . $class;
        }
        else {
            throw new InvalidArgumentException('Driver "' . $class . '" not found.');
        }

        $oauth = new $class($config);

        return $this->oauth[$name] = $oauth;
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
            if (file_exists($this->pluginManifestOfDrivers)) {
                /** @noinspection PhpIncludeInspection */
                $classes = include $this->pluginManifestOfDrivers;
                $this->pluginDrivers = isset($classes['SocialMedia']) ? $classes['SocialMedia'] : [];
            }
            else {
                $this->pluginDrivers = [];
            }
        }

        return isset($this->pluginDrivers[$class]) && count($this->pluginDrivers[$class]) == 1 ? $this->pluginDrivers[$class][0] : null;
    }
}