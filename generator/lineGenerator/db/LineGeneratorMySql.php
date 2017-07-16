<?php

namespace DBCast\generator\lineGenerator\db;

use DBCast\generator\lineGenerator\dbSyntax\DbSyntaxMySql;
use DBCast\generator\lineGenerator\LineGeneratorBase;

/**
 * Class LineGeneratorMySql
 * @package DBCast\generator\lineGenerator\db
 */
class LineGeneratorMySql extends LineGeneratorBase
{

    /**
     * Get lines for template
     * @return array
     */
    public function getLines()
    {
        $lines = array_merge(
            $this->getTablesAdded(),
            $this->getRelationDeleted(),
            $this->getIndexDeleted(),
            $this->getPrimaryKeyDeleted(),
            $this->getColumnsDeleted(),
            $this->getTablesDeleted(),
            $this->getTablesChanged()
        );

        // Sort columns line, with "AFTER" logic
        $columnsLinePool = array_merge_recursive(
            $this->getColumnsRenamed(),
            $this->getColumnsAdded(),
            $this->getColumnsChanged()
        );
        $lines = array_merge(
            $lines,
            static::sortColumnsLinePool($columnsLinePool)
        );


        $lines = array_merge(
            $lines,
            $this->getPrimaryKeyAdded(),
            $this->getIndexAdded(),
            $this->getRelationsAdded(),
            $this->getAutoIncrementChanged()
        );

        return $lines;
    }


    /**
     * Sort columns line, with "AFTER" logic
     * @param array $linesPool
     * @return array
     */
    public static function sortColumnsLinePool(array $linesPool)
    {
        $lines           = [];

        foreach ($linesPool as $tableName => $tableLinePoolData) {
            $columnsAreApply = [];
            foreach ($tableLinePoolData as $attributeName => $linePoolData) {

                if (empty($columnsAreApply[$attributeName])) {

                    // Get before column
                    if (!empty($linePoolData['after'])) {
                        $after = $linePoolData['after'];
                        if (!empty($tableLinePoolData[$after]) && empty($columnsAreApply[$after])) {
                            $beforePoolData          = $tableLinePoolData[$after];
                            $lines[]                 = $beforePoolData['line'];
                            $columnsAreApply[$after] = true;
                        }
                    }

                    $lines[]                         = $linePoolData['line'];
                    $columnsAreApply[$attributeName] = true;
                }
            }
        }


        return $lines;
    }


    /**
     * Get new tables
     * @return array
     */
    protected function getTablesAdded()
    {
        $lines  = [];
        $tables = $this->castDbDiff['tables'];
        foreach ($tables['new'] as $tableName => $table) {
            $line = null;
            $autoIncrement = null;

            $line .= '$tableOptions = \'ENGINE=' . $table['engine'] . '\' . PHP_EOL . "COLLATE ' . $table['encoding'] . '";' . PHP_EOL . PHP_EOL;

            $line .= '        $this->createTable(\'' . $tableName . '\', [' . PHP_EOL;
            foreach ($table['columns'] as $column => $columnData) {
                if (!empty($columnData['autoIncrement'])) {
                    $autoIncrement               = [
                        'column' => $column,
                        'data'   => $columnData
                    ];
                    $columnData['autoIncrement'] = false;
                }
                $line .= '            \'' . $column . '\' => "' . DbSyntaxMySql::createColumnSqlType($columnData) . '", ' . PHP_EOL;
            }
            $line .= '        ],$tableOptions);' . PHP_EOL . PHP_EOL;

            if (!empty($table['comment'])) {
                $line .= '        $this->addCommentOnTable(\'' . $tableName . '\', \'' . $table['comment'] . '\');' . PHP_EOL;
            }
            if (!empty($table['primaryKey'])) {
                $line .= '        $this->addPrimaryKey(\'PK_' . $table['name'] . '\', \'' . $tableName . '\', ' . json_encode($table['primaryKey']) . ');' . PHP_EOL;

            }
            if (!empty($autoIncrement)) {
                $line .= '        $this->alterColumn(\'' . $tableName . '\', \'' . $autoIncrement['column'] . '\', "' . DbSyntaxMySql::createColumnSqlType($autoIncrement['data'], true) . '");' . PHP_EOL;
            }

            if (!empty($table['indexes'])) {
                $indexes = $table['indexes'];
                if (!empty($indexes['PRIMARY'])) {
                    unset($indexes['PRIMARY']);
                }
                foreach ($indexes as $indexName => $indexValue) {
                    $line .= '        $this->createIndex(\'' . $indexName . '\', \'' . $tableName . '\', ' . json_encode($indexValue['columns']) . ', ' . ($indexValue['unique'] ? 'true' : 'false') . ');' . PHP_EOL;
                }
            }

            $lines[] = $line;
        }

        return $lines;
    }


    /**
     * @return array
     */
    protected function getRelationDeleted()
    {
        $lines     = [];
        $relations = $this->castDbDiff['relations'];

        if (!empty($relations['deleted'])) {
            foreach ($relations['deleted'] as $tableName => $relationsTemp) {
                if (!empty($relationsTemp)) {
                    foreach ($relationsTemp as $relationName => $relationValue) {
                        $lines[] = '$this->dropForeignKey(\'' . $relationName . '\', \'' . $tableName . '\');';
                    }
                }
            }
        }

        return $lines;
    }

    /**
     * @return array
     */
    protected function getIndexDeleted()
    {
        $lines   = [];
        $indexes = $this->castDbDiff['indexes'];

        if (!empty($indexes['deleted'])) {
            foreach ($indexes['deleted'] as $tableName => $indexesTemp) {
                if (!empty($indexesTemp)) {
                    foreach ($indexesTemp as $indexName => $indexValue) {
                        $lines[] = '$this->dropIndex(\'' . $indexName . '\', \'' . $tableName . '\');';
                    }
                }
            }
        }

        return $lines;
    }

    /**
     * @return array
     */
    protected function getPrimaryKeyDeleted()
    {
        $lines      = [];
        $primaryKey = $this->castDbDiff['primaryKey'];

        if (!empty($primaryKey['delete'])) {
            foreach ($primaryKey['delete'] as $tableName => $pkData) {
                $lines[] = '$this->dropPrimaryKey(\'PRIMARY\', \'' . $tableName . '\');';
            }
        }

        return $lines;
    }


    /**
     * @return array
     */
    protected function getColumnsDeleted()
    {
        $lines   = [];
        $columns = $this->castDbDiff['columns'];

        if (!empty($columns['delete'])) {
            foreach ($columns['delete'] as $tableName => $columnsTemp) {
                if (!empty($columnsTemp)) {
                    foreach ($columnsTemp as $columnName => $columnValue) {
                        $lines[] = '$this->dropColumn(\'' . $tableName . '\', \'' . $columnName . '\');';
                    }
                }
            }
        }

        return $lines;
    }


    /**
     * @return array
     */
    protected function getTablesDeleted()
    {
        $lines  = [];
        $tables = $this->castDbDiff['tables'];

        if (!empty($tables['deleted'])) {
            foreach ($tables['deleted'] as $dropTableName) {
                $lines[] = '$this->dropTable("' . $dropTableName . '");';
            }
        }

        return $lines;
    }

    /**
     * @return array
     */
    protected function getTablesChanged()
    {
        $lines  = [];
        $tables = $this->castDbDiff['tables'];

        if (!empty($tables['change'])) {
            foreach ($tables['change'] as $table) {
                $lines[] = '$this->db->createCommand(\'ALTER TABLE `' . $table['name'] . '` ENGINE=' . $table['engine'] . ' COLLATE ' . $table['encoding'] . '\')->execute();';
                $lines[] = '$this->addCommentOnTable(\'' . $table['name'] . '\', \'' . $table['comment'] . '\');';
            }
        }

        return $lines;
    }

    /**
     * @return array
     */
    protected function getColumnsRenamed()
    {
        $linesPool = [];
        $columns   = $this->castDbDiff['columns'];

        if (!empty($columns['rename'])) {
            foreach ($columns['rename'] as $tableName => $columnsTemp) {
                if (!empty($columnsTemp)) {
                    foreach ($columnsTemp as $columnName => $columnData) {

                        if (empty($linesPool[$tableName])) {
                            $linesPool[$tableName] = [];
                        }
                        $line                  = [];
                        $oldAttributeName      = $columnData['from'];


                        if (!empty($columns['change'][$tableName][$oldAttributeName])) {
                            $columnChangeData                = $columns['change'][$tableName][$oldAttributeName];
                            $afterColumn                     = $columnChangeData['after'];
                            $linesPool[$columnName]['after'] = $afterColumn;
                            $line[]                          = '$this->alterColumn(\'' . $tableName . '\', \'' . $oldAttributeName . '\', "' . DbSyntaxMySql::createColumnSqlType($columnChangeData, true) . '");';
                            unset($this->castDbDiff['columns']['change'][$tableName][$oldAttributeName]);
                        }

                        $line[]                                           = '$this->renameColumn(\'' . $tableName . '\', \'' . $columnData['from'] . '\', \'' . $columnData['to'] . '\');';
                        $linesPool[$tableName][$columnData['to']]['line'] = implode(PHP_EOL, $line);
                    }
                }
            }
        }

        return $linesPool;
    }

    /**
     * @return array
     */
    protected function getColumnsChanged()
    {
        $linesPool = [];
        $columns   = $this->castDbDiff['columns'];

        if (!empty($columns['change'])) {
            foreach ($columns['change'] as $tableName => $columnsTemp) {
                if (!empty($columnsTemp)) {
                    foreach ($columnsTemp as $columnName => $columnData) {

                        $afterColumn = $columnData['after'];
                        if (empty($linesPool[$tableName])) {
                            $linesPool[$tableName] = [];
                        }
                        $linesPool[$tableName][$columnName] = [
                            'line'  => '$this->alterColumn(\'' . $tableName . '\', \'' . $columnName . '\', "' . DbSyntaxMySql::createColumnSqlType($columnData, true) . '");',
                            'after' => $afterColumn
                        ];
                    }
                }
            }
        }

        return $linesPool;
    }

    /**
     * @return array
     */
    protected function getColumnsAdded()
    {
        $linesPool = [];
        $columns   = $this->castDbDiff['columns'];

        if (!empty($columns['new'])) {
            foreach ($columns['new'] as $tableName => $columnsTemp) {
                if (!empty($columnsTemp)) {
                    foreach ($columnsTemp as $columnName => $columnData) {
                        $afterColumn            = $columnData['after'];
                        if (empty($linesPool[$tableName])) {
                            $linesPool[$tableName] = [];
                        }
                        $linesPool[$tableName][$columnName] = [
                            'line'  => '$this->addColumn(\'' . $tableName . '\', \'' . $columnName . '\', "' . DbSyntaxMySql::createColumnSqlType($columnData, true) . '");',
                            'after' => $afterColumn
                        ];
                    }
                }
            }
        }

        return $linesPool;
    }

    /**
     * @return array
     */
    protected function getPrimaryKeyAdded()
    {
        $lines      = [];
        $primaryKey = $this->castDbDiff['primaryKey'];

        if (!empty($primaryKey['new'])) {
            foreach ($primaryKey['new'] as $tableName => $primaryKeyTemp) {
                if (!empty($primaryKeyTemp)) {
                    foreach ($primaryKeyTemp as $primaryKeyData) {
                        $lines[] = '$this->addPrimaryKey(\'primary_' . $tableName . '\', \'' . $tableName . '\', ' . json_encode($primaryKeyData) . ');';
                    }
                }
            }
        }

        return $lines;
    }

    /**
     * @return array
     */
    protected function getIndexAdded()
    {
        $lines   = [];
        $indexes = $this->castDbDiff['indexes'];

        if (!empty($indexes['new'])) {
            foreach ($indexes['new'] as $tableName => $indexesTemp) {
                if (!empty($indexesTemp)) {
                    foreach ($indexesTemp as $indexName => $indexData) {
                        $lines[] = '$this->createIndex(\'' . $indexName . '\', \'' . $tableName . '\', ' . json_encode($indexData['columns']) . ', ' . ($indexData['unique'] ? 'true' : 'false') . ');';
                    }
                }
            }
        }

        return $lines;
    }

    /**
     * @return array
     */
    protected function getRelationsAdded()
    {
        $lines     = [];
        $relations = $this->castDbDiff['relations'];

        // Add relations
        $relationsNew = $relations['new'];
        if (!empty($tables['new'])) {
            foreach ($tables['new'] as $tableName => $tableData) {
                $relationsTemp = $tableData['relations'];
                if (!empty($relationsTemp)) {
                    foreach ($relationsTemp as $relationName => $relationData) {
                        if (!isset($relationsNew[$tableName])) {
                            $relationsNew[$tableName] = [];
                        }
                        $relationsNew[$tableName][$relationName] = $relationData;
                    }
                }
            }
        }
        if (!empty($relationsNew)) {
            foreach ($relationsNew as $tableName => $relationsTemp) {
                if (!empty($relationsTemp)) {
                    foreach ($relationsTemp as $relationName => $relationData) {
                        $lines[] = '$this->addForeignKey(\'' . $relationName . '\', \'' . $tableName . '\',\'' . $relationData['pk'] . '\', \'' . $relationData['ftable'] . '\', \'' . $relationData['fk'] . '\', \'' . $relationData['delete_rule'] . '\', \'' . $relationData['update_rule'] . '\');';
                    }
                }
            }
        }

        return $lines;
    }

    /**
     * @return array
     */
    protected function getAutoIncrementChanged()
    {
        $lines   = [];
        $columns = $this->castDbDiff['columns'];

        // AutoIncrement
        if (!empty($columns['autoIncrement'])) {
            foreach ($columns['autoIncrement'] as $tableName => $columnsTemp) {
                if (!empty($columnsTemp)) {
                    foreach ($columnsTemp as $columnName => $columnData) {
                        $lines[] = '$this->alterColumn(\'' . $tableName . '\', \'' . $columnName . '\', "' . DbSyntaxMySql::createColumnSqlType($columnData, true) . '");';
                    }
                }
            }
        }

        return $lines;
    }

}