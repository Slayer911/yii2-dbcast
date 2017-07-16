<?php


namespace DBCast\tests\_base;


/**
 * Class CastCleanerHelper
 * @package DBCast\tests\_base
 */
class CastCleanerHelper
{

    /**
     * Remove dynamic parameters from cast array
     * @param array $castDbArray
     * @return array
     */
    static function removeDynamicParams(array $castDbArray)
    {
        if (isset($castDbArray['time'])) {
            unset($castDbArray['time']);
        }
        foreach ($castDbArray['tables'] as $tableName => $tableData) {
            if (isset($tableData['create_time'])) {
                unset($castDbArray['tables'][$tableName]['create_time']);
            }
        }

        return $castDbArray;
    }

}