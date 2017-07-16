<?php

namespace DBCast\generator\lineGenerator\dbSyntax;


/**
 * Class DbSyntaxMySql
 * @package DBCast\generator\dbSyntax\db
 */
class DbSyntaxMySql
{

    /**
     * Create column sql-type
     * @param      $columnData
     * @param bool $withAfter
     * @return string
     */
    public static function createColumnSqlType($columnData, $withAfter = false)
    {
        $columnSql[] = $columnData['dbType'];
        if (empty($columnData['allowNull'])) {
            $columnSql[] = 'NOT NULL';
            if (strlen($columnData['defaultValue']) > 0) {
                $columnSql[] = 'DEFAULT ' . $columnData['defaultValue'];
            }
        } else {
            $columnSql[] = 'NULL';
            if (empty($columnData['defaultValue'])) {
                $columnSql[] = 'DEFAULT NULL';
            } else {
                $columnSql[] = 'DEFAULT \'' . $columnData['defaultValue'].'\'';
            }
        }

        if (!empty($columnData['autoIncrement'])) {
            $columnSql[] = 'AUTO_INCREMENT';
        }
        if (!empty($columnData['comment'])) {
            $columnSql[] = 'COMMENT \'' . $columnData['comment'] . '\'';
        }
        if (!empty($columnData['after']) && $withAfter) {
            $columnSql[] = 'AFTER ' . $columnData['after'];
        }

        return implode(' ', $columnSql);
    }

}