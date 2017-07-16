<?php

namespace DBCast\tests\_base\exceptions;

/**
 * Class ConfigFileNotFound
 */
class ConfigFileNotFound extends \Exception
{

    /**
     * ConfigFileNotFound constructor.
     * @param string         $filePath
     * @param int            $code
     * @param \Exception|null $previous
     */
    public function __construct($filePath, $code = 0, \Exception $previous = null)
    {
        $message = 'File "' . $filePath . '" not found';
        parent::__construct($message, $code, $previous);
    }

}