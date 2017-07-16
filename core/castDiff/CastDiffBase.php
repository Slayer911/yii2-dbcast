<?php


namespace DBCast\core\castDiff;


use DBCast\core\castDiff\interfaces\CastDiffInterface;

/**
 * Class CastDiffBase
 * @package DBCast\core\castDiff
 */
abstract class CastDiffBase implements CastDiffInterface
{

    /**
     * Old tables cast
     * @var array
     */
    protected $oldCast = [];

    /**
     * Current tables cast
     * @var array
     */
    protected $currentCast = [];

    /**
     * CastsDiff constructor.
     * @param $previousCast
     * @param $currentCast
     */
    public function __construct($previousCast, $currentCast)
    {
        $this->oldCast     = $previousCast['tables'];
        $this->currentCast = $currentCast['tables'];
    }

}