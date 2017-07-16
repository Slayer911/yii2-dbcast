<?php


namespace DBCast\core\castData\workers;

use DBCast\exceptions\FileWritePermissionException;

/**
 * Class DatFileWorker
 * For work with cast files
 * @package DBCast\core
 */
class DatFileWorker extends DataWorkerBase
{
    /**
     * Path to cast file
     * @var
     */
    protected $castFilePath;

    /**
     * CastWorker constructor.
     * @param $castFilePath
     */
    public function __construct($castFilePath)
    {
        $this->castFilePath = $castFilePath;
        // Create first db cast
        if ($this->isNeedCreateFirstCastSave()) {
            $this->saveCurrentCastData([]);
        }
    }


    /**
     * Check, if cast file not exist
     * @return bool
     */
    public function isNeedCreateFirstCastSave()
    {
        return !file_exists($this->castFilePath);
    }

    /**
     * Check, if file is writable
     * @throws FileWritePermissionException
     */
    public function checkDataWriteAccess()
    {
        if (!is_writable($this->castFilePath)) {
            throw new FileWritePermissionException($this->castFilePath);
        }
    }

    /**
     * Save current cast version
     * @param $currentCast
     * @return bool
     */
    public function saveCurrentCast($currentCast)
    {
        $allPrevCast = $this->getAllDbCasts();
        $allPrevCast[$currentCast['dbName']] = $currentCast;

        return $this->saveCurrentCastData($allPrevCast);
    }


    /**
     * Save current case data with full dbCast string data
     * @param $fileData
     * @return int
     */
    protected function saveCurrentCastData($fileData)
    {
        return file_put_contents($this->castFilePath, json_encode($fileData, JSON_UNESCAPED_UNICODE));
    }


    /**
     * Get all previous casts of current connection
     * @return array
     */
    public function getAllDbCasts()
    {
        $castles = [];
        if (file_exists($this->castFilePath)) {
            $data = file_get_contents($this->castFilePath);
            if (!$castles = @json_decode($data, true)) {
                $castles = [];
            }
        }

        return $castles;
    }


}