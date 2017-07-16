<?php

namespace DBCast\tests\_unitUniversal;

use DBCast\core\contract\CastControllerConfigContract;

/**
 * Class CastControllerConfigContractCest
 */
class UniversalCastControllerConfigContract extends _UniversalCestBase
{


    /**
     * Check empty config
     * @param \UnitTester $I
     */
    public function checkEmptyConfig(\UnitTester $I)
    {
        $config = [];
        $this->checkInvalidArgumentException($I, $config);
    }

    /**
     * Check config with worker (without PDO object)
     * @param \UnitTester $I
     */
    public function checkConfigWithWorkerOnly(\UnitTester $I)
    {
        // Without PDO object
        $config = [];

        // Worker
        $config['worker'] = $this->workerConfigObject->getConfig();

        $this->checkInvalidArgumentException($I, $config);
    }

    /**
     * Check correct config
     */
    public function checkCorrectConfig(\UnitTester $I)
    {
        $config = [];

        $config['worker'] = $this->workerConfigObject->getConfig();
        $config['db']     = $this->castDbConfigObject->getConfig();

        new CastControllerConfigContract($config);
    }


    /**
     * Check InvalidArgumentException
     * @param \UnitTester $I
     */
    protected function checkInvalidArgumentException(\UnitTester $I, $config)
    {
        $I->expectException(\InvalidArgumentException::class, function () use ($config) {
            new CastControllerConfigContract($config);
        });
    }

}
