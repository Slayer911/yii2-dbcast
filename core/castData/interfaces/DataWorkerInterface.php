<?php

namespace DBCast\core\castData\interfaces;

use DBCast\exceptions\WritePermissionException;

/**
 * Interface DataWorkerInterface
 * @package DBCast\core\castData\interfaces
 */
interface DataWorkerInterface
{

    /**
     * Check, if this first run, and need create cast of current DB
     * @return mixed
     */
    public function isNeedCreateFirstCastSave();

    /**
     * Check, if cast data may be sav
     * @return WritePermissionException
     */
    public function checkDataWriteAccess();


    /**
     * Save current cast version
     * @param $currentCast
     * @return mixed
     */
    public function saveCurrentCast($currentCast);

    /**
     * Get all previous casts of current connection
     * @return array
     */
    public function getAllDbCasts();
}