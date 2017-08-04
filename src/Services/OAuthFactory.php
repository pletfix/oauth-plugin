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
    private $pluginManifestOfClasses;

    /**
     * Create a new factory instance.
     *
     * @param string|null $pluginManifestOfClasses
     */
    public function __construct($pluginManifestOfClasses = null)
    {
        $this->pluginManifestOfClasses = $pluginManifestOfClasses ?: manifest_path('plugins/classes.php');
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
            if (file_exists($this->pluginManifestOfClasses)) {
                /** @noinspection PhpIncludeInspection */
                $classes = include $this->pluginManifestOfClasses;
                $this->pluginDrivers = isset($classes['SocialMediaDrivers']) ? $classes['SocialMediaDrivers'] : [];
            }
            else {
                $this->pluginDrivers = [];
            }
        }

        return isset($this->pluginDrivers[$class]) ? $this->pluginDrivers[$class] : null;
    }
}