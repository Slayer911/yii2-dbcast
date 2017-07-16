<?php


namespace DBCast\tests\_unitUniversal;


use DBCast\core\CastController;
use DBCast\generator\Generator;
use DBCast\helpers\ConsoleHelper;
use DBCast\tests\_base\CastCleanerHelper;

/**
 * Class UniversalMigration
 * @package DBCast\tests\_unitUniversal
 */
class UniversalMigration extends _UniversalCestBase
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
     * Check two vector of migrate (Up and Rollback)
     * @param \UnitTester $I
     */
    public function checkMigration(\UnitTester $I)
    {
        \Yii::$app = new FakeYiiApp($this->castController->castDb->getConnection());

        $this->workerConfigObject->restoreOldVersion();
        $this->castDbConfigObject->restoreNewVersion();

        $generator                = new Generator($this->castController);
        $generator->migrationPath = __DIR__ . '/../_output/';
        $generator->init();


        $fileObject       = $generator->generateFileObject();
        $migrateClassName = $generator->getFullMigrateName();

        ConsoleHelper::log($migrateClassName, 3);
        $fileObject->save();
        $filePath = $fileObject->path;
        ConsoleHelper::log($fileObject->path, 3);

        include_once($filePath);
        /**
         * @var $migrate \yii\db\Migration
         */
        $migrate = new $migrateClassName;

        // Rollback to previous
        ConsoleHelper::log('Rollback to previous cast', 3);
        $migrate->down();
        $this->castController->castDb->resetCache();
        $I->assertEquals(
            CastCleanerHelper::removeDynamicParams($this->castController->castDb->getCurrentCast()),
            CastCleanerHelper::removeDynamicParams($this->castController->castDb->getPrevCasts())
        );


        // Up to new
        ConsoleHelper::log('Up to new cast', 3);
        $migrate->up();
        $this->workerConfigObject->restoreNewVersion(); // Make previous cast as current
        $this->castController->castDb->resetCache();
        $I->assertEquals(
            CastCleanerHelper::removeDynamicParams($this->castController->castDb->getPrevCasts()),
            CastCleanerHelper::removeDynamicParams($this->castController->castDb->getCurrentCast())
        );


        // Remove test migration
        unlink($filePath);
    }

}


class FakeYiiApp
{

    public $db;
    public $cache;

    public function __construct($db)
    {
        $this->db = $db;
    }


    public function has($name)
    {
        return isset($this->{$name});
    }

    public function get($name)
    {
        return $this->{$name};
    }
}