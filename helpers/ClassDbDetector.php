<?php

namespace DBCast\helpers;


use DBCast\exceptions\UnsupportedDataBase;
use yii\db\Connection;

/**
 * Class ClassDbDetector
 * @package DBCast\core\helpers
 */
class ClassDbDetector
{

    /**
     * Detect class name for current connection by input classes
     * @param Connection $dbConnection
     * @param array      $classes
     * @return mixed
     * @throws UnsupportedDataBase
     */
    public static function detectClass(Connection $dbConnection, array $classes)
    {
        $dbType = $dbConnection->getDriverName();
        if (!empty($classes[$dbType])) {
            $castDbClassName = $classes[$dbType];
        } else {
            throw new UnsupportedDataBase($dbType);
        }

        return $castDbClassName;
    }

}