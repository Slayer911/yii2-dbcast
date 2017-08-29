<?php


namespace DBCast\generator;


use yii\base\UnknownPropertyException;
use yii\gii\CodeFile;

/**
 * Class CastFile
 * @package DBCast\generator
 */
class CastCodeFile extends CodeFile
{

    /**
     * @var Generator
     */
    protected $generatorObject;

    /**
     * CastFile constructor
     * @param string $path
     * @param string $content
     * @param        $generatorObject
     */
    public function __construct($path, $content, $config = [], $generatorObject)
    {
        $this->generatorObject = $generatorObject;
        parent::__construct($path, $content, $config);
    }


    /**
     * Save file with note in DB
     * @return bool|string
     */
    public function save()
    {

        if ($this->saveFile()) {
            // Create note in DB about new migrate as accepted
            $this->generatorObject->noteMigrationAsAccept($this->path);

            return true;
        }

        return false;       
    }


    /**
     * Saves the code into the file specified by [[path]].
     * @return string|bool the error occurred while saving the code file, or true if no error.
     */
    public function saveFile()
    {
        $newDirMode  = 0777;
        $newFileMode = 0666;
        if (!empty(\Yii::$app) && !empty(\Yii::$app->controller)) {
            $module      = \Yii::$app->controller->module;
            if(!empty($module->newFileMode)){
                $newFileMode = $module->newFileMode;
            }
            if(!empty($module->newDirMode)){
                $newDirMode  = $module->newDirMode;
            }
        }

        if ($this->operation === self::OP_CREATE) {
            $dir = dirname($this->path);
            if (!is_dir($dir)) {
                $mask   = @umask(0);
                $result = @mkdir($dir, $newDirMode, true);
                @umask($mask);
                if (!$result) {
                    return "Unable to create the directory '$dir'.";
                }
            }
        }
        if (@file_put_contents($this->path, $this->content) === false) {
            return "Unable to write the file '{$this->path}'.";
        } else {
            $mask = @umask(0);
            @chmod($this->path, $newFileMode);
            @umask($mask);
        }

        return true;
    }

}
