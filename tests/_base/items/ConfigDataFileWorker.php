<?php


namespace DBCast\tests\_base\items;


use DBCast\helpers\ConsoleHelper;
use DBCast\tests\_base\ConfigCastBase;
use DBCast\tests\_base\interfaces\ConfigDataWorkerInterface;

/**
 * Class ConfigDataFileWorker
 * @package DBCast\tests\_base\items
 */
class ConfigDataFileWorker extends ConfigCastBase implements ConfigDataWorkerInterface
{

    /**
     * Base config name
     * @var
     */
    protected $baseConfigName = 'fileWorkerConfig';

    /**
     * Restore cast file
     */
    protected function restore($restoreFile)
    {
        ConsoleHelper::log('Restore cast file ' . $restoreFile, 3);
        $templateData = file_get_contents($restoreFile);
        file_put_contents($this->config['file'], $templateData);
    }

}