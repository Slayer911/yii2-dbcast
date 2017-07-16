<?php


namespace DBCast\tests\_base\interfaces;


/**
 * Interface ConfigCastBaseInterface
 * @package DBCast\tests\_base\interfaces
 */
interface ConfigCastBaseInterface
{

    /**
     * ConfigCastBaseInterface constructor.
     * @param null $configName
     */
    public function __construct($configName = null);

    /**
     * Get config
     * @return mixed
     */
    public function getConfig();


    /**
     * Restore old version
     * @return mixed
     */
    public function restoreOldVersion();


    /**
     * Restore new version
     * @return mixed
     */
    public function restoreNewVersion();

}