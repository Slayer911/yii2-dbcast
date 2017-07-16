<?php

namespace DBCast\tests\_unitUniversal;

use DBCast\tests\_base\interfaces\ConfigDataWorkerInterface;
use DBCast\tests\_base\interfaces\ConfigDbInterface;

/**
 * Class UniversalCestBase
 * @package DBCast\tests\unit\_universal
 */
abstract class _UniversalCestBase
{

    /**
     * @var ConfigDataWorkerInterface
     */
    protected $workerConfigObject;

    /**
     * @var ConfigDbInterface
     */
    protected $castDbConfigObject;

    /**
     * _UniversalCestBase constructor.
     * @param ConfigDataWorkerInterface $workerConfigObject
     * @param ConfigDbInterface         $castDbConfigObject
     */
    public function __construct(ConfigDataWorkerInterface $workerConfigObject, ConfigDbInterface $castDbConfigObject)
    {
        $this->workerConfigObject = $workerConfigObject;
        $this->castDbConfigObject = $castDbConfigObject;
    }

}