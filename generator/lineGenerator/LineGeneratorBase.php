<?php

namespace DBCast\generator\lineGenerator;

/**
 * Class LineGeneratorBase
 * @package DBCast\generator\lineGenerator
 */
class LineGeneratorBase
{

    /**
     * Current castDiff array
     * @var array
     */
    protected $castDbDiff;

    /**
     * LineGeneratorBase constructor.
     * @param array $castDbDiff
     */
    public function __construct(array $castDbDiff)
    {
        $this->castDbDiff = $castDbDiff;
    }

}