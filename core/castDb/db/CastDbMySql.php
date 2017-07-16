<?php

namespace DBCast\core\castDb\db;


use yii\helpers\ArrayHelper;


/**
 * Class CastDbMySql
 * get current/previous db cast
 * @package console\vendors\dbDiff
 * @author  slayer911@inbox.ru
 */
class CastDbMySql extends CastDbBase
{


    /**
     * Get current Cast
     * @return mixed
     */
    public function getCurrentCast()
    {
        if (!isset($this->_cache['currentCast'])) {
            $tables = $this->getTableSchema();
            if (!empty($tables)) {
                foreach ($tables as $tableName => $tableSchema) {
                    $tables[$tableName]['primaryKey'] = $this->getPrimaryKey($tableName);
                    $tables[$tableName]['columns']    = $this->getColumnsSchema($tableName);
                    $tables[$tableName]['indexes']    = $this->getIndex($tableName);
                    $tables[$tableName]['relations']  = $this->getRelations($tableName);
                }
            }
            $this->_cache['currentCast'] = [
                'dbName' => $this->getCurrentDbName(),
                'time'   => time(),
                'tables' => $tables
            ];
        }


        return $this->_cache['currentCast'];
    }


    /**
     * Get tables schema
     * @return mixed
     */
    protected function getTableSchema()
    {
        if (!isset($this->_cache['tablesSchema'])) {
            $tablesSchema                 = $this->db
                ->createCommand('SELECT * FROM information_schema.TABLES t WHERE t.TABLE_SCHEMA=SCHEMA() AND t.TABLE_TYPE=\'BASE TABLE\'')
                ->queryAll();
            $this->_cache['tablesSchema'] = [];
            if (!empty($tablesSchema)) {
                foreach ($tablesSchema as $tableSchemaItem) {
                    $this->_cache['tablesSchema'][$tableSchemaItem['TABLE_NAME']] = [
                        'name'        => $tableSchemaItem['TABLE_NAME'],
                        'engine'      => $tableSchemaItem['ENGINE'],
                        'create_time' => $tableSchemaItem['CREATE_TIME'],
                        'update_time' => $tableSchemaItem['UPDATE_TIME'],
                        'encoding'    => $tableSchemaItem['TABLE_COLLATION'],
                        'comment'     => $tableSchemaItem['TABLE_COMMENT'],
                    ];
                }
            }
        }

        return $this->_cache['tablesSchema'];
    }


    /**
     * Get table schema
     * @param $tableName
     * @return mixed
     */
    protected function getColumnsSchema($tableName)
    {
        $cacheName = 'columnSchema_' . $tableName;
        if (!isset($this->_cache[$cacheName])) {
            $tableSchema = $this->db->getTableSchema($tableName, true);
            if (!empty($tableSchema->columns)) {
                $after                    = null;
                $this->_cache[$cacheName] = array_map(function ($val) use (&$after) {
                    $newVal = [
                        'name'          => $val->name,
                        'allowNull'     => $val->allowNull,
                        'dbType'        => $val->dbType,
                        'defaultValue'  => $val->defaultValue,
                        'autoIncrement' => $val->autoIncrement,
                        'unsigned'      => $val->unsigned,
                        'comment'       => $val->comment,
                        'after'         => $after,
                    ];
                    $after  = $val->name;

                    return $newVal;
                }, $tableSchema->columns);
            }
        }

        return $this->_cache[$cacheName];
    }


    /**
     * Get table indexes
     * @param $tableName
     * @return mixed
     */
    protected function getIndex($tableName)
    {
        $cacheName = 'indexes_' . $tableName;
        if (!isset($this->_cache[$cacheName])) {
            $indexes = $this->db
                ->createCommand('SHOW INDEX FROM ' . $tableName)
                ->queryAll();

            $indexesSorted = [];
            if (!empty($indexes)) {
                foreach ($indexes as $indexValue) {
                    $keyName = $indexValue['Key_name'];
                    if ($keyName == 'PRIMARY') {
                        continue;
                    }
                    $columnNumber = ($indexValue['Seq_in_index'] - 1);
                    if (!isset($indexesSorted[$keyName])) {
                        $indexesSorted[$keyName] = [
                            'columns' => [
                                $columnNumber => $indexValue['Column_name']
                            ],
                            'unique'  => (int)(!(bool)$indexValue['Non_unique']),
                        ];
                    } else {
                        $indexesSorted[$keyName]['columns'][$columnNumber] = $indexValue['Column_name'];
                    }
                }
            }
            $this->_cache[$cacheName] = $indexesSorted;
        }


        return $this->_cache[$cacheName];
    }


    /**
     * Get primary keys
     * @param $tableName
     * @return array|\string[]
     */
    protected function getPrimaryKey($tableName)
    {
        $tableSchemaObject = $this->db->getTableSchema($tableName, true);

        return ($tableSchemaObject->primaryKey) ? $tableSchemaObject->primaryKey : [];
    }

    /**
     * Get relations
     * @param $tableName
     * @return array
     * // TODO: incorrect work with multi-index relations
     */
    protected function getRelations($tableName)
    {
        $relations = $this->getRelationData();

        return !empty($relations[$tableName]) ? $relations[$tableName] : [];
    }


    /**
     * Get relations from all tables (optimization)
     * @return mixed
     */
    protected function getRelationData()
    {

        if (!isset($this->_cache['relations'])) {
            $keysUsage   = $this->db->createCommand('SELECT  `CONSTRAINT_NAME`,`TABLE_SCHEMA`, `TABLE_NAME`, `COLUMN_NAME`, `REFERENCED_TABLE_SCHEMA`,  `REFERENCED_TABLE_NAME`, `REFERENCED_COLUMN_NAME`
FROM `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`
WHERE
  `TABLE_SCHEMA` = SCHEMA() AND `REFERENCED_TABLE_NAME` IS NOT NULL')->queryAll();
            $referential = $this->db->createCommand('SELECT CONSTRAINT_NAME,UPDATE_RULE,DELETE_RULE FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE `CONSTRAINT_SCHEMA` = SCHEMA()')->queryAll();
            $referential = ArrayHelper::index($referential, 'CONSTRAINT_NAME');


            $relations = [];
            if (!empty($keysUsage)) {
                foreach ($keysUsage as $key => $keyUseData) {
                    $tableName = $keyUseData['TABLE_NAME'];
                    $fkName    = $keyUseData['CONSTRAINT_NAME'];
                    if (isset($referential[$fkName])) {
                        if (empty($relations[$tableName])) {
                            $relations[$tableName] = [];
                        }
                        $relations[$tableName][$fkName] = [
                            'name'        => $keyUseData['CONSTRAINT_NAME'],
                            'table'       => $keyUseData['REFERENCED_TABLE_NAME'],
                            'ftable'      => $keyUseData['REFERENCED_TABLE_NAME'],
                            'pk'          => $keyUseData['COLUMN_NAME'],
                            'fk'          => $keyUseData['REFERENCED_COLUMN_NAME'],
                            'update_rule' => $referential[$fkName]['UPDATE_RULE'],
                            'delete_rule' => $referential[$fkName]['DELETE_RULE']
                        ];
                    }
                }
            }
            $this->_cache['relations'] = $relations;
        }

        return $this->_cache['relations'];
    }


    /**
     * Get current dbName
     * @return mixed
     */
    protected function getCurrentDbName()
    {
        if (!isset($this->_cache['dbName'])) {
            $this->_cache['dbName'] = $this->db->createCommand('SELECT DATABASE()')->queryScalar();
        }

        return $this->_cache['dbName'];
    }

}