<?php

namespace DBCast\generator\lineGenerator;

/**
 * Interface LineGeneratorInterface
 */
interface LineGeneratorInterface
{

    /**
     * LineGeneratorInterface constructor.
     * @param array $castDiffData
     */
    public function __construct(array $castDiffData);


    /**
     * Get lines for template
     * @return array
     */
    public function getLines();

}