<?php

namespace DBCast\core\castDb\db;


use DBCast\core\castData\interfaces\DataWorkerInterface;
use DBCast\core\castDb\interfaces\DbInterface;
use DBCast\exceptions\UnsupportedDataBase;
use yii\db\Connection;

/**
 * Class CastDbBase
 * @package DBCast\core\db
 */
abstract class CastDbBase implements DbInterface
{


    /**
     * Yii DAO
     * @var Connection
     */
    protected $db;


    /**
     * CastDataWorker object
     * @var DataWorkerInterface
     */
    protected $castDataWorker;

    /**
     * Cache
     * @var array
     */
    protected $_cache = [];


    /**
     * CastsDb constructor.
     * @param Connection          $dbConnection
     * @param DataWorkerInterface $castDataWorker
     * @throws UnsupportedDataBase
     */
    public function __construct(Connection $dbConnection, DataWorkerInterface $castDataWorker)
    {
        $this->db             = $dbConnection;
        $this->castDataWorker = $castDataWorker;

        // Put first cast in file
        if (!$this->castDataWorker->getAllDbCasts()) {
            $this->saveCurrentCast();
        }
    }


    /**
     * Get connection object
     * @return Connection
     */
    public function getConnection()
    {
        return $this->db;
    }


    /**
     * Get previous casts
     * @return array|mixed
     */
    public function getPrevCasts()
    {
        $allCastles = $this->castDataWorker->getAllDbCasts();
        $prevCastle = !empty($allCastles[$this->getCurrentDbName()]) ? $allCastles[$this->getCurrentDbName()] : [];

        return $prevCastle;
    }

    /**
     * Save current cast version
     * @return bool
     */
    public function saveCurrentCast()
    {
        $currentCast = $this->getCurrentCast();
        if ($this->castDataWorker->saveCurrentCast($currentCast)) {
            $this->resetCache();

            return true;
        }

        return false;
    }

    /**
     * Reset Object cache
     */
    public function resetCache()
    {
        $this->_cache = [];
    }


    /**
     * Get current dbName
     * @return mixed
     */
    abstract protected function getCurrentDbName();
}