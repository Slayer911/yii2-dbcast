<?php


namespace DBCast\tests\_unitUniversal;


use DBCast\core\CastController;
use DBCast\tests\_base\CastCleanerHelper;

/**
 * Class UniversalCastController
 * @package DBCast\tests\_unitUniversal
 */
class UniversalCastController extends _UniversalCestBase
{
    /**
     * @var CastController
     */
    protected $castController;

    /**
     * Init
     */
    public function init(\UnitTester $I)
    {
        $this->castController = new CastController([
            'db'     => $this->castDbConfigObject->getConfig(),
            'worker' => $this->workerConfigObject->getConfig()
        ]);
    }

    /**
     * Check new cast with standard cast for this db dump
     * @param \UnitTester $I
     */
    public function checkOldVersionEquals(\UnitTester $I)
    {
        $this->castDbConfigObject->restoreOldVersion();
        $this->workerConfigObject->restoreOldVersion();
        $this->castController->castDb->resetCache();
        $currentCast  = $this->castController->castDb->getCurrentCast();
        $previousCast = $this->castController->castDb->getPrevCasts();

        $I->assertEquals(CastCleanerHelper::removeDynamicParams($currentCast), CastCleanerHelper::removeDynamicParams($previousCast));
    }


    /**
     * Check cast changes
     * @param \UnitTester $I
     */
    public function checkCastChanges(\UnitTester $I)
    {
        $this->workerConfigObject->restoreOldVersion();
        $this->castDbConfigObject->restoreNewVersion();
        $this->castController->castDb->resetCache();

        $currentCast  = $this->castController->castDb->getCurrentCast();
        $previousCast = $this->castController->castDb->getPrevCasts();

        $I->assertNotEquals(CastCleanerHelper::removeDynamicParams($currentCast), CastCleanerHelper::removeDynamicParams($previousCast));
    }


    /**
     * Check CastDiff
     * @param \UnitTester $I
     */
    public function checkCastDiff(\UnitTester $I)
    {
        $this->workerConfigObject->restoreOldVersion();
        $this->castDbConfigObject->restoreNewVersion();
        $this->castController->castDb->resetCache();

        $castsDiff = $this->castController->createCastDbDiffObject();
        $diff      = $castsDiff->getDiff();

        $I->assertTrue((is_array($diff) && $diff));
    }

}