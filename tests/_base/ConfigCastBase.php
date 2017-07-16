<?php


namespace DBCast\tests\_base;

use DBCast\tests\_base\interfaces\ConfigCastBaseInterface;

/**
 * Class ConfigCastBase
 * @package DBCast\tests\_base
 */
abstract class ConfigCastBase implements ConfigCastBaseInterface
{

    /**
     * Config array
     * @var
     */
    protected $config;

    /**
     * Base config name
     * @var
     */
    protected $baseConfigName;

    /**
     * ConfigDbMySqlConfig constructor.
     * @param $configName
     */
    public function __construct($configName = null)
    {
        if (!$configName) {
            $configName = $this->baseConfigName;
        }
        $this->config = ConfigFilesHelper::getConfig($configName);
    }

    /**
     * Get config
     * @return mixed
     */
    public function getConfig()
    {
        $config = $this->config;
        foreach ($config as $key => $value) {
            if (preg_match('/^_/', $key)) {
                unset($config[$key]);
            }
        }

        return $config;
    }

    /**
     * Check config attribute
     * @param $attributeName
     */
    protected function checkConfigAttribute($attributeName)
    {
        if (empty($this->config[$attributeName])) {
            throw new \InvalidArgumentException('Empty argument \'' . $attributeName . '\' in ' . $this->baseConfigName . '.php config');
        }
    }

    /**
     * Restore old data
     */
    public function restoreOldVersion()
    {
        $attributeName = '_restore-old';
        $this->checkConfigAttribute($attributeName);

        $this->restore($this->config[$attributeName]);
    }

    /**
     * Restore new data
     */
    public function restoreNewVersion()
    {
        $attributeName = '_restore-new';
        $this->checkConfigAttribute($attributeName);

        $this->restore($this->config[$attributeName]);
    }

    /**
     * Restore data by template file
     * @param $restoreFile
     * @return mixed
     */
    abstract protected function restore($restoreFile);

}