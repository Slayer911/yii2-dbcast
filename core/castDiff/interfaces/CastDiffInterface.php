<?php

namespace DBCast\core\castDiff\interfaces;

/**
 * Interface CastDiffInterface
 * @package DBCast\core\castDiff\interfaces
 */
interface CastDiffInterface
{

    /**
     * CastsDiff constructor.
     * @param $previousCast
     * @param $currentCast
     */
    public function __construct($previousCast, $currentCast);

    /**
     * Get all difference by two casts
     * @return array
     */
    public function getDiff();


    /**
     * Check if casts have differences
     * @return bool
     */
    public function isHaveDiff();


    /**
     * Get changed tables
     * @return array
     */
    public function getTablesForChange();

}