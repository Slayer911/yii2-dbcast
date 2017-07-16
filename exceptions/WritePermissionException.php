<?php

namespace DBCast\exceptions;


/**
 * Class WritePermissionException
 * @package DBCast\exceptions
 */
class WritePermissionException extends \Exception
{

    /**
     * FileWritePermissionException constructor.
     * @param string          $saveDataWorkerPresentation
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct($saveDataWorkerPresentation, $code = 0, \Exception $previous = null)
    {
        $message = $saveDataWorkerPresentation . ' does not have write permissions';
        parent::__construct($message, $code, $previous);
    }


}