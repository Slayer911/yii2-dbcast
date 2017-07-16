<?php

namespace DBCast\generator;


use DBCast\core\CastController;
use DBCast\generator\lineGenerator\LineGeneratorFabric;
use DBCast\generator\lineGenerator\LineGeneratorInterface;
use Yii;
use yii\base\View;
use yii\gii\CodeFile;
use yii\helpers\StringHelper;

/**
 * Class Generator
 * @package Umcms\gii\generators\migrationCasts
 * @author  slayer911@inbox.ru
 */
class Generator
{

    /**
     * Path for migration save
     * @var string
     */
    public $migrationPath = '@console/migrations';

    /**
     * Migration name
     * @var string
     */
    public $migrationName = null;

    /**
     * Migration full name
     * @var
     */
    protected $migrationFullName;

    /**
     * Use table prefix
     * @var bool
     */
    public $useTablePrefix = false;

    /**
     * CastsDb object
     * @var CastController
     */
    public $castController;

    /**
     * Today time presentation for migration name
     * @var
     */
    protected $baseMigrationTime;

    /**
     * Unique number for migrate
     * @var int
     */
    protected $uniqueMigrateTime = 0;

    /**
     * Line generator
     * @var LineGeneratorInterface
     */
    protected $lineGenerator;


    /**
     * Cache
     * @var array
     */
    protected $_cache = [];


    /**
     * Generator constructor.
     * @param $castController
     */
    public function __construct($castController)
    {
        $this->castController = $castController;
    }


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->baseMigrationTime = gmdate('ymd_');
        $migrationPath           = Yii::getAlias($this->migrationPath);
        $files                   = glob($migrationPath . DIRECTORY_SEPARATOR . 'm' . $this->baseMigrationTime . '*');

        $maxMigrateTime = 0;
        if (!empty($files)) {
            foreach ($files as $fileName) {
                if (preg_match('/(?<=m[0-9]{6}_)[0-9]{6}/', $fileName, $migrationTimeTemp)) {
                    if ($maxMigrateTime < $migrationTimeTemp[0]) {
                        $maxMigrateTime = $migrationTimeTemp[0];
                    }
                }
            }
        }

        $this->uniqueMigrateTime = (int)$maxMigrateTime;
    }


    /**
     * Generate file object
     * @return CastCodeFile|false
     */
    public function generateFileObject()
    {
        $lineGenerator        = $this->createLineGenerator();
        $lineGeneratorReverse = $this->createLineGenerator(true);
        $lines                = $lineGenerator->getLines();
        $linesReverse         = $lineGeneratorReverse->getLines();


        // Another changes
        if ($file = $this->generateFileObjectByData('migrate', [
            'lines'        => $lines,
            'linesReverse' => $linesReverse,
        ])
        ) {
            return $file;
        }


        return false;
    }

    /**
     * Create lineGenerator
     * @param bool $reverse
     * @return LineGeneratorInterface
     */
    protected function createLineGenerator($reverse = false)
    {
        $castDiff            = $this->castController->createCastDbDiffObject($reverse)->getDiff();
        $lineGeneratorFabric = new LineGeneratorFabric($this->castController->castDb->getConnection());
        $lineGenerator       = $lineGeneratorFabric->getLineGeneratorObject($castDiff);

        return $lineGenerator;
    }


    /**
     * Get migrate time
     * @return string
     */
    public function getMigrationTime()
    {
        $this->uniqueMigrateTime = (int)$this->uniqueMigrateTime;
        $this->uniqueMigrateTime++;
        if (strlen($this->uniqueMigrateTime) > 6) {
            $this->uniqueMigrateTime = 1;
        }

        $migrateTime = (string)$this->uniqueMigrateTime;
        while (strlen($migrateTime) < 6) {
            $migrateTime = '0' . $migrateTime;
        }

        return $this->baseMigrationTime . $migrateTime;
    }


    /**
     * Make note in db about created migration as accepted
     * @param $migrateFile
     * @return bool|int
     * @throws \yii\db\Exception
     */
    public function noteMigrationAsAccept($migrateFile)
    {
        $migrationName = StringHelper::basename($migrateFile);
        $migrationName = strtok($migrationName, '.');
        if (!empty($migrationName)) {
            return $this->castController->castDb->getConnection()->createCommand()->insert('migration', [
                'version'    => $migrationName,
                'apply_time' => time()
            ])->execute();
        }

        return false;
    }


    /**
     * Generates the table name by considering table prefix.
     * If [[useTablePrefix]] is false, the table name will be returned without change.
     * @param string $tableName the table name (which may contain schema prefix)
     * @return string the generated table name
     */
    public function generateTableName($tableName)
    {
        if (!$this->useTablePrefix) {
            return $tableName;
        }

        $db = $this->castController->castDb->getConnection();
        if (preg_match("/^{$db->tablePrefix}(.*?)$/", $tableName, $matches)) {
            $tableName = '{{%' . $matches[1] . '}}';
        } elseif (preg_match("/^(.*?){$db->tablePrefix}$/", $tableName, $matches)) {
            $tableName = '{{' . $matches[1] . '%}}';
        }

        return $tableName;
    }


    /**
     * Generate one file object
     * @param       $templateName
     * @param array $templateParams
     * @return CodeFile|false
     */
    protected function generateFileObjectByData($templateName, array $templateParams = [])
    {
        $migrationFullName = $this->getFullMigrateName();
        $fileData          = $this->render($templateName, [
                'migrationName' => $migrationFullName
            ] + $templateParams);

        if ($fileData) {
            $filePath   = rtrim(Yii::getAlias($this->migrationPath), '/') . "/{$migrationFullName}.php";
            $fileObject = new CastCodeFile($filePath, $fileData, [], $this);

            return $fileObject;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getFullMigrateName()
    {
        if (!$this->migrationFullName) {
            $this->migrationFullName = 'm' . $this->getMigrationTime() . '_castDb' . ($this->migrationName ? '_' . $this->migrationName : null);
        }

        return $this->migrationFullName;
    }


    /**
     * Render file
     * @param       $viewFileName
     * @param array $parameters
     * @return string
     */
    protected function render($viewFileName, array $parameters = [])
    {
        if (!isset($this->_cache['viewObject'])) {
            $view                       = new View();
            $view->context              = $this;
            $this->_cache['viewObject'] = $view;
        } else {
            $view = $this->_cache['viewObject'];
        }

        $filePath = $this->getTemplatePath() . DIRECTORY_SEPARATOR . $viewFileName . '.php';

        return $view->renderPhpFile($filePath, $parameters);
    }


    /**
     * Get template path
     * @return string
     */
    public function getTemplatePath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'templates';
    }

}