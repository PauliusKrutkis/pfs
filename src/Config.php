<?php


namespace Pfs;


class Config
{
    const DIR = 'config';

    const REGISTRY = [
        'enqueue'
    ];

    protected static $storage;
    protected static $pluginDir;

    public static function get($key)
    {
        if ( ! in_array($key, self::REGISTRY)) {
            throw new \Exception("No {$key} is bound in the configuration.");
        }

        return self::$storage[$key];
    }

    public static function load($directory)
    {
        self::$pluginDir = $directory;

        foreach (self::REGISTRY as $config) {
            self::$storage[$config] = require self::$pluginDir . '/' . self::DIR . '/' . $config . '.php';
        }
    }

    public static function getPluginDir()
    {
        return self::$pluginDir;
    }
}