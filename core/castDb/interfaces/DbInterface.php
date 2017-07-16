<?php

namespace DBCast\core\castDb\interfaces;

use DBCast\core\castData\interfaces\DataWorkerInterface;
use yii\db\Connection;

/**
 * Interface DbInterface
 * @package DBCast\core\interfaces
 */
interface DbInterface
{

    /**
     * DbInterface constructor.
     * @param Connection          $dbConnection
     * @param DataWorkerInterface $castDataWorker
     */
    public function __construct(Connection $dbConnection, DataWorkerInterface $castDataWorker);

    /**
     * Get connection object
     * @return Connection
     */
    public function getConnection();

    /**
     * Reset Object cache
     */
    public function resetCache();

    /**
     * Get previous casts
     * @return array|mixed
     */
    public function getPrevCasts();

    /**
     * Get current Cast
     * @return mixed
     */
    public function getCurrentCast();

    /**
     * Save current cast version
     * @return bool
     */
    public function saveCurrentCast();


}