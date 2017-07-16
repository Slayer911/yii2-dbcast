<?php


namespace DBCast\exceptions;

use Exception;

/**
 * Class UnsupportedDataBase
 * @package DBCast\exceptions
 */
class UnsupportedDataBase extends \Exception
{

    /**
     * Current DB type
     * @var
     */
    protected $currentDataBase;


    /**
     * UnsupportedDataBase constructor.
     * @param string         $currentDataBase
     * @param int            $code
     * @param Exception|null $previous
     */
    public function __construct($currentDataBase, $code = 0, Exception $previous = null)
    {
        $message = 'Current "' . $currentDataBase . '" database unsupported by DBCast yet.';
        parent::__construct($message, $code, $previous);
    }

}