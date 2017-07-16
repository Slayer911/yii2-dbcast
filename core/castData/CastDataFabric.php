<?php


namespace DBCast\core\castData;


use DBCast\core\castData\interfaces\DataWorkerInterface;
use DBCast\core\castData\workers\DatFileWorker;

/**
 * Class CastDataFabric
 * @package DBCast\core\castData
 */
class CastDataFabric
{

    /**
     * Cast data config
     * @var array
     */
    protected $config;

    /**
     * Supported type of data workers
     * @var array
     */
    static $supportedDataWorkerTypes = [
        'file' => DatFileWorker::class
    ];

    /**
     * CastDataFabric constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }


    /**
     * Get cast worker object
     * @return DataWorkerInterface
     */
    public function getCastWorkerObject()
    {
        if (empty($this->config['type'])) {
            throw new \InvalidArgumentException('config[\'type\'] must be filled');
        }

        switch ($this->config['type']) {
            case 'file':
                if (empty($this->config['file'])) {
                    throw new \InvalidArgumentException('config[\'file\'] must be filled, when selected type is "file"');
                }
                $castDataWorker = new DatFileWorker($this->config['file']);

                return $castDataWorker;
                break;
        }

        throw new \InvalidArgumentException('config[\'type\'] must be one of the next types:' . implode(array_keys(static::$supportedDataWorkerTypes)));
    }

}