<?php

namespace DBCast\core\castDiff;


use DBCast\core\castDiff\db\CastDiffMySql;
use DBCast\core\castDiff\interfaces\CastDiffInterface;
use DBCast\helpers\ClassDbDetector;
use yii\db\Connection;

/**
 * Class CastDiffFabric
 * @package DBCast\core\castDiff
 */
class CastDiffFabric
{

    /**
     * Databases, which support DBCast
     * @var array
     */
    static $supportedDataBases = [
        'mysql' => CastDiffMySql::class
    ];

    /**
     * @var string
     */
    protected $currentClassName;

    /**
     * CastDiffFabric constructor.
     * @param Connection $dbConnection
     */
    public function __construct(Connection $dbConnection)
    {
        $this->currentClassName = ClassDbDetector::detectClass($dbConnection, static::$supportedDataBases);
    }


    /**
     * Get cast diff object
     * @param array $previousDbCast
     * @param array $currentDbCast
     * @return CastDiffInterface
     */
    public function getCastDiffObject(array $previousDbCast, array $currentDbCast)
    {
        /**
         * @var CastDiffInterface $castDiff
         */
        $castDiff = new $this->currentClassName($previousDbCast, $currentDbCast);

        return $castDiff;
    }

}