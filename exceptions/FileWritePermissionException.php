<?php

namespace DBCast\exceptions;


/**
 * Class FileWritePermissionException
 * @package DBCast\exceptions
 */
class FileWritePermissionException extends WritePermissionException
{

    /**
     * FileWritePermissionException constructor.
     * @param string         $filePath
     * @param int            $code
     * @param \Exception|null $previous
     */
    public function __construct($filePath, $code = 0, \Exception $previous = null)
    {
        $message = 'File "' . $filePath . '"';
        parent::__construct($message, $code, $previous);
    }


}