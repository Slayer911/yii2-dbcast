<?php

namespace DBCast\generator\lineGenerator;


use DBCast\generator\lineGenerator\db\LineGeneratorMySql;
use DBCast\helpers\ClassDbDetector;
use yii\db\Connection;

/**
 * Class LineGeneratorFabric
 * @package DBCast\generator\lineGenerator
 */
class LineGeneratorFabric
{

    /**
     * Databases, which support DBCast
     * @var array
     */
    static $supportedDataBases = [
        'mysql' => LineGeneratorMySql::class
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
     * Get lines generator for current database type
     * @param array $castDIffArray
     * @return LineGeneratorInterface
     */
    public function getLineGeneratorObject(array $castDIffArray)
    {
        /**
         * @var LineGeneratorInterface $castDIff
         */
        $castDIff = new $this->currentClassName($castDIffArray);

        return $castDIff;
    }

}