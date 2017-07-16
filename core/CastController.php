<?php

namespace DBCast\core;

use DBCast\core\castData\CastDataFabric;
use DBCast\core\castData\interfaces\DataWorkerInterface;
use DBCast\core\castDb\CastDbFabric;
use DBCast\core\castDb\interfaces\DbInterface;
use DBCast\core\castDiff\CastDiffFabric;
use DBCast\core\contract\CastControllerConfigContract;

/**
 * Class CastController
 * @package DBCast\core
 */
class CastController
{

    /**
     * Config of cast module
     * @var CastControllerConfigContract
     */
    protected $config;

    /**
     * CastDb object
     * @var DbInterface
     */
    public $castDb;

    /**
     * CastDataWorker object
     * @var DataWorkerInterface
     */
    protected $castDataWorker;


    protected $castDbDiffFabric;


    /**
     * CastController constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = new CastControllerConfigContract($config);

        $castDataWorkerFabric = new CastDataFabric($this->config->getWorkerConfig());
        $this->castDataWorker = $castDataWorkerFabric->getCastWorkerObject();

        $dbConfig = $this->config->getDbConfig();
        if (is_array($dbConfig)) {
            $dbConfig = \Yii::createObject($dbConfig);
        }
        $castDbFabric = new CastDbFabric($dbConfig, $this->castDataWorker);
        $this->castDb = $castDbFabric->getCastDbObject();
    }


    /**
     * Create castDiff object
     * @param bool $reverse
     * @return castDiff\interfaces\CastDiffInterface
     */
    public function createCastDbDiffObject($reverse = false)
    {
        if (!$this->castDbDiffFabric) {
            $this->castDbDiffFabric = new CastDiffFabric($this->castDb->getConnection());
        }

        if (!$reverse) {
            $castDbDiff = $this->castDbDiffFabric->getCastDiffObject(
                $this->castDb->getPrevCasts(),
                $this->castDb->getCurrentCast()
            );
        } else {
            $castDbDiff = $this->castDbDiffFabric->getCastDiffObject(
                $this->castDb->getCurrentCast(),
                $this->castDb->getPrevCasts()
            );
        }


        return $castDbDiff;
    }

}