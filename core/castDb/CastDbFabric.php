<?php


namespace DBCast\core\castDb;


use DBCast\core\castData\interfaces\DataWorkerInterface;
use DBCast\core\castDb\db\CastDbMySql;
use DBCast\core\castDb\interfaces\DbInterface;
use DBCast\helpers\ClassDbDetector;
use yii\db\Connection;

/**
 * Class CastDbFabric
 * @package DBCast\core\castDb
 */
class CastDbFabric
{

    /**
     * Db connection
     * @var Connection
     */
    protected $dbConnection;

    /**
     * Databases, which support DBCast
     * @var array
     */
    static $supportedDataBases = [
        'mysql' => CastDbMySql::class
    ];

    /**
     * CastDbFabric constructor.
     * @param Connection          $dbConnection
     * @param DataWorkerInterface $castDataWorker
     */
    public function __construct(\yii\db\Connection $dbConnection, DataWorkerInterface $castDataWorker)
    {
        $this->dbConnection   = $dbConnection;
        $this->castDataWorker = $castDataWorker;
    }


    /**
     * Get CastDb object
     * @return DbInterface
     */
    public function getCastDbObject()
    {
        $castDbClassName = ClassDbDetector::detectClass($this->dbConnection,static::$supportedDataBases);

        /**
         * @var DbInterface $castDbObject
         */
        $castDbObject = new $castDbClassName($this->dbConnection, $this->castDataWorker);

        return $castDbObject;
    }

}