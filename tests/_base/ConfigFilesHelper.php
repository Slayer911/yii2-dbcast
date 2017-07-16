<?php


namespace DBCast\tests\_base;
use DBCast\tests\_base\exceptions\ConfigFileNotFound;

/**
 * Class ConfigFilesHelper
 * For work with config files
 * @package DBCast\tests\_base
 */
class ConfigFilesHelper
{

    /**
     * Get directory with config
     * @return string
     */
    protected static function getConfigDir()
    {
        $dir = __DIR__ . '/../_config';

        return $dir;
    }

    /**
     * Get config file path
     * @param $configName
     * @return string
     */
    protected static function getConfigPath($configName)
    {
        $configDir  = static::getConfigDir();
        $configPath = $configDir . DIRECTORY_SEPARATOR . $configName . '.php';

        return $configPath;
    }


    /**
     * Get config data by file name (without extension. Default extension is ".php")
     * @param $configName
     * @return mixed
     * @throws ConfigFileNotFound
     */
    public static function getConfig($configName)
    {
        $configPath = static::getConfigPath($configName);

        if (!file_exists($configPath)) {
            throw new ConfigFileNotFound($configPath);
        }

        return require($configPath);
    }

}