<?php

namespace DBCast\core\castDiff\db;

use DBCast\core\castDiff\CastDiffBase;


/**
 * Class CastDiffMySql
 * @package DBCast\core\castDiff\db\mySql
 */
class CastDiffMySql extends CastDiffBase
{

    /**
     * Get all difference by two casts
     * @return array
     */
    public function getDiff()
    {
        $originalData = [
            'old'     => $this->oldCast,
            'current' => $this->currentCast,
        ];

        $changes = [
            'tables'    => [],
            'columns'   => [],
            'indexes'   => [],
            'relations' => [],
        ];

        // Tables
        $changes['tables']['new']     = $this->getTablesForCreate();
        $changes['tables']['deleted'] = $this->getTablesForDelete();
        $changes['tables']['change']  = $this->getTablesForChange();

        // Columns : new, deleted, renamed(with the same type and attributes) and changed columns
        $changes['columns'] = $this->getColumnsDiff();

        // Change renamed columns name in Index (MySql make it automatic)
        if (!empty($changes['columns']['rename'])) {
            $this->changeRenamedColumnsIndex($changes['columns']['rename']);
        }

        // Indexes
        $changes['indexes']['new']     = $this->getNewIndexes();
        $changes['indexes']['deleted'] = $this->getDeletedIndexes();
        $this->getChangedIndexes($changes['indexes']['new'], $changes['indexes']['deleted']);

        // PK
        $changes['primaryKey'] = $this->getPrimaryKeysDiff();

        // Relations
        $changes['relations']['new']     = $this->getNewRelations();
        $changes['relations']['deleted'] = $this->getDeletedRelations();
        // Add relations from new tables
        if (!empty($changes['tables']['new'])) {
            foreach ($changes['tables']['new'] as $tableName => $tableData) {
                if (!empty($tableData['relations'])) {
                    if (empty($changes['relations']['new'][$tableName])) {
                        $changes['relations']['new'][$tableName] = [];
                    }
                    foreach ($tableData['relations'] as $relationName => $relationData) {
                        $changes['relations']['new'][$tableName][$relationName] = $relationData;
                    }
                }
            }
        }


        // Changed relation by Change column name
        $changes['relations'] = $this->recreateFkOfRenamedColumns($changes['columns']['rename'], $changes['relations']);

        $this->oldCast     = $originalData['old'];
        $this->currentCast = $originalData['current'];

        return $changes;
    }


    /**
     * Check if casts have differences
     * @return bool
     */
    public function isHaveDiff()
    {
        $diff = $this->getDiff();
        foreach ($diff as $itemType => $itemValue) {
            if (!empty($itemValue)) {
                foreach ($itemValue as $item) {
                    if (!empty($item)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }


    /**
     * Recreate relations, which have renamed columns
     * @param $renamedColumns
     * @param $changedRelations
     * @return mixed
     */
    public function recreateFkOfRenamedColumns($renamedColumns, $changedRelations)
    {
        if (!empty($renamedColumns)) {
            foreach ($renamedColumns as $tableName => $renamedColumnsData) {

                $renamedColumnIndex = [];
                if (!empty($renamedColumnsData)) {
                    foreach ($renamedColumnsData as $renamedColumnsItemData) {
                        $renamedColumnIndex[$renamedColumnsItemData['from']] = $renamedColumnsItemData['to'];
                    }
                }


                foreach ($this->oldCast as $tableNameOld => $tableDataOld) {
                    $relations = $tableDataOld['relations'];
                    if (!empty($relations)) {
                        foreach ($relations as $relationName => $relationData) {
                            if (isset($renamedColumnIndex[$relationData['pk']]) || isset($renamedColumnIndex[$relationData['fk']])) {
                                if (empty($changedRelations['new'][$tableName])) {
                                    $changedRelations['new'][$tableName] = [];
                                }
                                if (empty($changedRelations['deleted'][$tableName])) {
                                    $changedRelations['deleted'][$tableName] = [];
                                }

                                $newValue        = $relationData;
                                $newRelationName = $relationName;
                                if (isset($renamedColumnIndex[$relationData['pk']])) {
                                    $newRelationName = str_replace($newValue['pk'], $renamedColumnIndex[$relationData['pk']], $newRelationName);
                                    $newValue['pk']  = $renamedColumnIndex[$relationData['pk']];
                                }
                                if (isset($renamedColumnIndex[$relationData['fk']])) {
                                    $newRelationName = str_replace($newValue['fk'], $renamedColumnIndex[$relationData['fk']], $newRelationName);
                                    $newValue['fk']  = $renamedColumnIndex[$relationData['fk']];
                                }

                                $changedRelations['deleted'][$tableNameOld][$relationName] = $newValue;
                                $changedRelations['new'][$tableNameOld][$newRelationName]  = $newValue;
                            }
                        }
                    }
                }
            }
        }

        return $changedRelations;
    }


    /**
     * Change renamed columns in index's (MySql rename columns in Index automatic)
     * @param $renamedColumns
     */
    public function changeRenamedColumnsIndex($renamedColumns)
    {
        if (!empty($renamedColumns)) {
            foreach ($renamedColumns as $tableName => $renamedColumnsData) {

                $renamedColumnIndex = [];
                if (!empty($renamedColumnsData)) {
                    foreach ($renamedColumnsData as $renamedColumnsItemData) {
                        $renamedColumnIndex[$renamedColumnsItemData['from']] = $renamedColumnsItemData['to'];
                    }
                }

                // Index
                if (!empty($this->oldCast[$tableName]) && !empty($this->oldCast[$tableName]['indexes'])) {
                    foreach ($this->oldCast[$tableName]['indexes'] as $indexName => $indexValue) {
                        if (!empty($indexValue['columns'])) {
                            foreach ($indexValue['columns'] as $indexValueColumnKey => $indexValueColumnName) {
                                if (isset($renamedColumnIndex[$indexValueColumnName])) {
                                    $this->oldCast[$tableName]['indexes'][$indexName]['columns'][$indexValueColumnKey] = $renamedColumnIndex[$indexValueColumnName];
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    /**
     * Get Pk diff
     * @return array
     */
    public function getPrimaryKeysDiff()
    {
        $pk = [];
        foreach ($this->currentCast as $tableName => $tableData) {
            $oldTableData = $this->oldCast[$tableName];

            if ($tableData['primaryKey'] != $oldTableData['primaryKey']) {
                $type = null;
                if (empty($oldTableData['primaryKey'])) {
                    $type = 'new';
                } elseif (empty($tableData['primaryKey'])) {
                    $type = 'delete';
                } else {
                    if (empty($pk['delete'])) {
                        $pk['delete'] = [];
                    }
                    $pk['delete'][$tableName] = $tableData['primaryKey'];
                    $type                     = 'new';
                }

                if (empty($pk[$type])) {
                    $pk[$type] = [];
                }
                $pk[$type][$tableName] = [
                    'columns' => $tableData['primaryKey'],
                ];
            }
        }

        return $pk;
    }


    /**
     * Get changed tables
     * @return array
     */
    public function getTablesForChange()
    {
        $changedTables = [];
        foreach ($this->currentCast as $tableName => $tableData) {
            $oldTableData = $this->oldCast[$tableName];

            if (
                $tableData['engine'] != $oldTableData['engine']
                || $tableData['engine'] != $oldTableData['engine']
                || $tableData['encoding'] != $oldTableData['encoding']
                || $tableData['comment'] != $oldTableData['comment']
            ) {
                $changedTables[$tableName] = [
                    'name'     => $tableName,
                    'engine'   => $tableData['engine'],
                    'encoding' => $tableData['encoding'],
                    'comment'  => $tableData['comment'],
                ];
            }
        }

        return $changedTables;
    }


    /**
     * Get removed relations
     * @return array
     */
    public function getDeletedRelations()
    {
        $removedRelations = [];
        foreach ($this->oldCast as $tableName => $tableData) {
            $relations        = $tableData['relations'];
            $currentRelations = $this->currentCast[$tableName]['relations'];
            if (!empty($relations)) {
                foreach ($relations as $relationName => $relationValue) {
                    if (!isset($currentRelations[$relationName])) {
                        if (empty($removedRelations[$tableName])) {
                            $removedRelations[$tableName] = [];
                        }
                        $removedRelations[$tableName][$relationName] = $relationValue;
                    }
                }
            }
        }

        return $removedRelations;
    }


    /**
     * Get new relations
     * @return array
     */
    public function getNewRelations()
    {
        $newRelations = [];
        foreach ($this->currentCast as $tableName => $tableData) {
            $relations    = $tableData['relations'];
            $oldRelations = $this->oldCast[$tableName]['relations'];
            if (!empty($relations)) {
                foreach ($relations as $relationName => $relationValue) {
                    if (!isset($oldRelations[$relationName])) {
                        if (empty($newRelations[$tableName])) {
                            $newRelations[$tableName] = [];
                        }
                        $newRelations[$tableName][$relationName] = $relationValue;
                    }
                }
            }
        }

        return $newRelations;
    }


    /**
     * Get new indexes
     * @return array
     */
    public function getNewIndexes()
    {
        $newIndexes = [];
        foreach ($this->currentCast as $tableName => $tableData) {
            $indexes  = $tableData['indexes'];
            $oldIndex = $this->oldCast[$tableName]['indexes'];
            if (!empty($indexes)) {
                foreach ($indexes as $indexName => $indexValue) {
                    if (!isset($oldIndex[$indexName])) {
                        if (empty($newIndexes[$tableName])) {
                            $newIndexes[$tableName] = [];
                        }
                        $newIndexes[$tableName][$indexName] = $indexValue;
                    }
                }
            }
        }

        return $newIndexes;
    }


    /**
     * Check changed index
     * @param $newIndexes
     * @param $removedIndexes
     */
    public function getChangedIndexes(&$newIndexes, &$removedIndexes)
    {
        foreach ($this->currentCast as $tableName => $tableData) {
            $indexes  = $tableData['indexes'];
            $oldIndex = $this->oldCast[$tableName]['indexes'];
            if (!empty($indexes)) {
                foreach ($indexes as $indexName => $indexValue) {
                    if (isset($oldIndex[$indexName])) {
                        if ($oldIndex[$indexName] != $indexValue) {
                            if (empty($removedIndexes[$tableName])) {
                                $removedIndexes[$tableName] = [];
                            }
                            if (empty($newIndexes[$tableName])) {
                                $newIndexes[$tableName] = [];
                            }
                            $newIndexes[$tableName][$indexName]     = $indexValue;
                            $removedIndexes[$tableName][$indexName] = $indexValue;
                        }
                    }
                }
            }
        }
    }

    /**
     * Get removed indexes
     * @return array
     */
    public function getDeletedIndexes()
    {
        $removedIndexes = [];
        foreach ($this->oldCast as $tableName => $tableData) {
            $indexes      = $tableData['indexes'];
            $currentIndex = $this->currentCast[$tableName]['indexes'];
            if (!empty($indexes)) {
                foreach ($indexes as $indexName => $indexValue) {
                    if (!isset($currentIndex[$indexName])) {
                        if (empty($removedIndexes[$tableName])) {
                            $removedIndexes[$tableName] = [];
                        }
                        $removedIndexes[$tableName][$indexName] = $indexValue;
                    }
                }
            }
        }

        return $removedIndexes;
    }


    /**
     * Get difference of columns
     * @return array
     */
    public function getColumnsDiff()
    {
        $deletedColumns    = $this->getColumnsForDelete();
        $newColumns        = $this->getColumnsForCreate();
        $renamedColumns    = static::searchRenamedColumns($deletedColumns, $newColumns);
        $changedColumns    = $this->getColumnsForChange();
        $newAutoIncrements = $this->getNewAutoIncrementColumns($newColumns, $changedColumns);


        $result = [
            'delete'        => $deletedColumns,
            'new'           => $newColumns,
            'change'        => $changedColumns,
            'rename'        => $renamedColumns,
            'autoIncrement' => $newAutoIncrements
        ];

        // Delete empty array
        foreach ($result as $type => $tables) {
            if (!empty($tables)) {
                foreach ($tables as $tableName => $data) {
                    if (empty($data)) {
                        unset($result[$type][$tableName]);
                    }
                }
            }
        }

        return $result;
    }


    /**
     * Make correctives considering autoIncrement
     * @param $newColumns
     * @param $changedColumns
     * @return array
     */
    protected function getNewAutoIncrementColumns(&$newColumns, &$changedColumns)
    {
        $newAutoIncrementColumns = [];
        $data                    = [
            'new'    => $newColumns,
            'change' => $changedColumns,
        ];
        foreach ($data as $type => $tables) {
            if (!empty($tables)) {
                foreach ($tables as $tableName => $changedColumnsTemp) {
                    if (!empty($changedColumnsTemp)) {
                        foreach ($changedColumnsTemp as $columnName => $columnData) {
                            $oldAutoIncrement = false;
                            if ($type == 'change') {
                                $oldAutoIncrement = $this->oldCast[$tableName]['columns'][$columnName]['autoIncrement'];
                            }
                            $newAutoIncrement = $this->currentCast[$tableName]['columns'][$columnName]['autoIncrement'];
                            if (!empty($newAutoIncrement)) {
                                if ((string)$oldAutoIncrement != (string)$newAutoIncrement) {
                                    if (empty($newAutoIncrementColumns[$tableName])) {
                                        $newAutoIncrementColumns[$tableName] = [];
                                    }
                                    switch ($type) {
                                        case 'new':
                                            $newAutoIncrementColumns[$tableName][$columnName]     = $columnData;
                                            $newColumns[$tableName][$columnName]['autoIncrement'] = false;
                                            break;
                                        case 'change':
                                            $newAutoIncrementColumns[$tableName][$columnName] = $columnData;
                                            unset($changedColumns[$tableName][$columnName]);
                                            break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $newAutoIncrementColumns;
    }


    /**
     * Find renamed columns from new and deleted
     * @param $deletedColumns
     * @param $newColumns
     * @return mixed
     */
    protected static function searchRenamedColumns(&$deletedColumns, &$newColumns)
    {
        $renamedColumns = [];
        if (!empty($newColumns)) {
            foreach ($newColumns as $tableName => $newsColumnData) {
                if (!empty($newsColumnData)) {
                    foreach ($newsColumnData as $currentColumnName => $newColumnData) {

                        $newColumnAfter            = $newColumnData['after'];
                        $deletedColumnsInThisTable = !empty($deletedColumns[$tableName]) ? $deletedColumns[$tableName] : [];
                        if (!empty($deletedColumnsInThisTable)) {
                            foreach ($deletedColumnsInThisTable as $deletedColumnData) {

                                // Same position of new and deleted column - check type of column
                                if ($deletedColumnData['after'] == $newColumnAfter) {
                                    // Type's of columns the same - move column for changed list
                                    if (
                                        $deletedColumnData['dbType'] == $newColumnData['dbType']
                                        && $deletedColumnData['defaultValue'] == $newColumnData['defaultValue']
                                        && $deletedColumnData['autoIncrement'] == $newColumnData['autoIncrement']
                                        && $deletedColumnData['unsigned'] == $newColumnData['unsigned']
                                    ) {
                                        if (empty($changedColumns[$tableName])) {
                                            $renamedColumns[$tableName] = [];
                                        }
                                        $renamedColumns[$tableName][$currentColumnName] = [
                                            'from' => $deletedColumnData['name'],
                                            'to'   => $currentColumnName
                                        ];
                                        unset($newColumns[$tableName][$currentColumnName]);
                                        unset($deletedColumns[$tableName][$deletedColumnData['name']]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $renamedColumns;
    }


    /**
     * Get created columns
     *  * they also may be deleted from this array, and moved to columns changed list
     * @return array
     */
    protected function getColumnsForCreate()
    {
        $newColumns = [];
        foreach ($this->currentCast as $tableTitle => $tableData) {
            $currentColumns  = !empty($tableData['columns']) ? $tableData['columns'] : [];
            $oldTableData    = $this->oldCast[$tableTitle];
            $oldTableColumns = !empty($oldTableData['columns']) ? $oldTableData['columns'] : [];
            foreach ($currentColumns as $columnTitle => $columnData) {
                if (!isset($oldTableColumns[$columnTitle])) {
                    if (empty($newColumns[$tableTitle])) {
                        $newColumns[$tableTitle] = [];
                    }
                    $newColumns[$tableTitle][$columnTitle] = $columnData;
                }
            }
        }

        return $newColumns;
    }


    /**
     * Get changed columns
     * @return array
     */
    protected function getColumnsForChange()
    {
        $changedColumns = [];
        foreach ($this->currentCast as $tableTitle => $tableData) {
            $currentColumns  = !empty($tableData['columns']) ? $tableData['columns'] : [];
            $oldTableData    = $this->oldCast[$tableTitle];
            $oldTableColumns = !empty($oldTableData['columns']) ? $oldTableData['columns'] : [];
            foreach ($currentColumns as $columnTitle => $columnData) {
                if (isset($oldTableColumns[$columnTitle])) {
                    $oldColumn  = $oldTableColumns[$columnTitle];
                    $needChange = false;
                    foreach ($columnData as $key => $value) {
                        if (is_array($value) || is_array($oldColumn[$key])) {
                            if ((array)$oldColumn[$key] !== (array)$value) {
                                $needChange = true;
                                break;
                            }
                        } elseif ((string)$oldColumn[$key] !== (string)$value) {
                            $needChange = true;
                            break;
                        }
                    }

                    if (!empty($needChange)) {
                        if (empty($changedColumns[$tableTitle])) {
                            $changedColumns[$tableTitle] = [];
                        }
                        $changedColumns[$tableTitle][$columnTitle] = $columnData;
                    }
                }
            }
        }

        return $changedColumns;
    }


    /**
     * Get deleted columns
     *  * they also may be deleted from this array, and moved to columns changed list
     * @return array
     */
    protected function getColumnsForDelete()
    {
        $deletedColumns = [];
        foreach ($this->oldCast as $tableName => $tableData) {
            $oldColumns     = !empty($tableData['columns']) ? $tableData['columns'] : [];
            $currentData    = $this->currentCast[$tableName];
            $currentColumns = !empty($currentData['columns']) ? $currentData['columns'] : [];
            foreach ($oldColumns as $columnTitle => $columnData) {
                if (!isset($currentColumns[$columnTitle])) {
                    if (empty($deletedColumns[$tableName])) {
                        $deletedColumns[$tableName] = [];
                    }
                    $deletedColumns[$tableName][$columnTitle] = $columnData;
                }
            }
        }

        return $deletedColumns;
    }


    /**
     * Get new tables
     * @return array
     */
    protected function getTablesForCreate()
    {
        $newTables = [];
        foreach ($this->currentCast as $tableName => $tableValue) {
            if (empty($this->oldCast[$tableName])) {
                $newTables[$tableName] = $tableValue;
                unset($this->currentCast[$tableName]);
            }
        }

        return $newTables;
    }


    /**
     * Get tables for delete
     * @return array
     */
    protected function getTablesForDelete()
    {
        $tablesForDelete = [];
        foreach ($this->oldCast as $tableName => $tableValue) {
            if (empty($this->currentCast[$tableName])) {
                $tablesForDelete[$tableName] = $tableName;
                unset($this->oldCast[$tableName]);
            }
        }

        return $tablesForDelete;
    }

}