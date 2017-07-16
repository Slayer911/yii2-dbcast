<?php
namespace DBCast\controllers;

use DBCast\core\CastController;
use DBCast\generator\Generator;
use DBCast\helpers\ConsoleHelper;
use yii\console\Exception;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Class MigrateController
 * @package console\controllers
 */
class MigrateController extends \yii\console\controllers\MigrateController
{


    /**
     * Develop mode (dbCast check on)
     * @var bool
     */
    public $develop = false;


    /**
     * Cache
     * @var array
     */
    protected $_cache = [];


    /**
     * Path for file with dbCast
     * @var string
     */
    public $filePathTemplate = '@runtime/dbCastle.json';


    /**
     * Init
     */
    public function init()
    {
        \Yii::$app->db->createCommand('SET SESSION wait_timeout = 400')->execute();
        parent::init();
    }


    /**
     * Save current db cast without commit changes
     * @return bool
     */
    public function actionCastSave()
    {
        if ($this->getCastController()->createCastDbDiffObject()->isHaveDiff()) {
            if ($this->confirm('Do you want to rewrite current db cast without creating new migration for changes ?')) {
                if ($this->getCastController()->castDb->saveCurrentCast()) {
                    $this->stdout("Success\n", Console::FG_YELLOW);
                }
            } else {
                ConsoleHelper::log('For commit changes type command "php yii migrate/cast-commit"', 3);

                return false;
            }
        } else {
            ConsoleHelper::log('Success', 3);
        }

        return true;
    }


    /**
     * Show casts diff
     */
    public function actionCastDiff()
    {
        $this->stdout("CastDiff\n", Console::FG_YELLOW);
        $diff = $this->getCastController()->createCastDbDiffObject()->getDiff();
        $this->makeArrayPresentation($diff);
    }


    /**
     * Show current casts
     */
    public function actionCastCurrent()
    {
        $this->stdout("CastDiff\n", Console::FG_YELLOW);
        $this->makeArrayPresentation($this->getCastController()->castDb->getCurrentCast());
    }


    /**
     * Show previous casts
     */
    public function actionCastPrevious()
    {
        $this->stdout("CastDiff\n", Console::FG_YELLOW);
        $this->makeArrayPresentation($this->getCastController()->castDb->getPrevCasts());
    }


    /**
     * Create migrations for new changes
     * @param null $migrationName
     * @return bool
     */
    public function actionCastCommit($migrationName = null)
    {
        $this->stdout("CastCommit" . PHP_EOL . PHP_EOL, Console::FG_YELLOW);

        top:
        $generator                = new Generator($this->getCastController());
        $generator->migrationName = $migrationName;
        $generator->init();
        if ($this->getCastController()->createCastDbDiffObject()->isHaveDiff() && $file = $generator->generateFileObject()) {

            // Show file
            $this->stdout(PHP_EOL . $file->path . PHP_EOL, Console::FG_GREEN);
            $this->stdout(PHP_EOL . $file->content . PHP_EOL, Console::FG_CYAN);
            echo PHP_EOL . PHP_EOL;

            // Ask about creating

            switch ($this->select('Create this migrations ?', ['y' => 'Yes', 'n' => 'No', 'c' => 'Change migrate name'])) {
                case 'y':

                    // Create new file
                    $this->stdout('Save : ' . $file->path . PHP_EOL, Console::FG_GREEN);
                    $file->save();

                    // Save new DbCast
                    $this->getCastController()->castDb->saveCurrentCast();
                    break;

                case 'c':
                    // Repeat migrate with specific migration name
                    $migrationName = $this->prompt('Input migration name');

                    goto top;
                    break;

                case 'n':
                    return false;
            }

        } else {
            $this->stdout("No changes occurred" . PHP_EOL, Console::FG_GREEN);
        }

        return true;
    }


    /**
     * Resolve changes of DB schema
     * @return bool
     */
    protected function actionResolveCastChanges()
    {
        top:
        $this->stdout("\n You have changes in your DB schema (DbCast)" . PHP_EOL, Console::BG_RED);
        switch ($this->select('What do you want to do with changes ?', ['c' => 'Commit changes', 's' => 'Save current schema without commit', 'd' => 'Show different'/*, 'r' => 'Rollback changes'*/])) {
            case 'c':
                if (!$this->actionCastCommit()) {
                    goto top;
                }
                break;

            case 's':
                if (!$this->actionCastSave()) {
                    goto top;
                }
                break;
            case 'd':
                $this->actionCastDiff();
                goto top;
                break;
        }

        return true;
    }


    /**
     * Redefinition yii base "Action down" command
     * @param int $limit
     * @return int
     * @throws Exception
     */
    public function actionDown($limit = 1)
    {
        top:
        if ($limit === 'all') {
            $limit = null;
        } else {
            $limit = (int)$limit;
            if ($limit < 1) {
                throw new Exception('The step argument must be greater than 0.');
            }
        }

        $migrations = $this->getMigrationHistory($limit);

        if (empty($migrations)) {
            $this->stdout("No migration has been done before.\n", Console::FG_YELLOW);

            return ExitCode::OK;
        }

        $migrations = array_keys($migrations);

        $n = count($migrations);
        $this->stdout("Total $n " . ($n === 1 ? 'migration' : 'migrations') . " to be reverted:\n", Console::FG_YELLOW);
        foreach ($migrations as $migration) {
            $this->stdout("\t$migration\n");
        }
        $this->stdout("\n");

        // DbCast check
        if ($this->develop && $this->getCastController()->createCastDbDiffObject()->isHaveDiff()) {
            $this->actionResolveCastChanges();
            goto top;
        }

        $reverted = 0;
        if ($this->confirm('Revert the above ' . ($n === 1 ? 'migration' : 'migrations') . '?')) {
            foreach ($migrations as $migration) {
                if (!$this->migrateDown($migration)) {
                    $this->stdout("\n$reverted from $n " . ($reverted === 1 ? 'migration was' : 'migrations were') . " reverted.\n", Console::FG_RED);
                    $this->stdout("\nMigration failed. The rest of the migrations are canceled.\n", Console::FG_RED);

                    return ExitCode::UNSPECIFIED_ERROR;
                }
                $reverted++;
            }
            $this->stdout("\n$n " . ($n === 1 ? 'migration was' : 'migrations were') . " reverted.\n", Console::FG_GREEN);
            $this->stdout("\nMigrated down successfully.\n", Console::FG_GREEN);
        }

        // Save new cast Db
        $this->getCastController()->castDb->saveCurrentCast();

        return ExitCode::OK;
    }


    /**
     * Redefinition yii base "Action up" command
     * @param int $limit
     * @return int
     */
    public function actionUp($limit = 0)
    {
        top:
        $migrations = $this->getNewMigrations();
        if (empty($migrations)) {
            $this->stdout("Not found new migrations. Your system is up-to-date.\n", Console::FG_GREEN);

            return ExitCode::OK;
        }

        $total = count($migrations);
        $limit = (int)$limit;
        if ($limit > 0) {
            $migrations = array_slice($migrations, 0, $limit);
        }

        $n = count($migrations);
        if ($n === $total) {
            $this->stdout("Total $n new " . ($n === 1 ? 'migration' : 'migrations') . " to be applied:\n", Console::FG_YELLOW);
        } else {
            $this->stdout("Total $n out of $total new " . ($total === 1 ? 'migration' : 'migrations') . " to be applied:\n", Console::FG_YELLOW);
        }

        foreach ($migrations as $migration) {
            $this->stdout("\t$migration\n");
        }
        $this->stdout("\n");

        // DbCast check
        if ($this->develop && $this->getCastController()->createCastDbDiffObject()->isHaveDiff()) {
            $this->actionResolveCastChanges();
            goto top;
        }

        $applied = 0;
        if ($this->confirm('Apply the above ' . ($n === 1 ? 'migration' : 'migrations') . '?')) {
            foreach ($migrations as $migration) {
                if (!$this->migrateUp($migration)) {
                    $this->stdout("\n$applied from $n " . ($applied === 1 ? 'migration was' : 'migrations were') . " applied.\n", Console::FG_RED);
                    $this->stdout("\nMigration failed. The rest of the migrations are canceled.\n", Console::FG_RED);

                    return ExitCode::UNSPECIFIED_ERROR;
                }
                $applied++;
            }

            $this->stdout("\n$n " . ($n === 1 ? 'migration was' : 'migrations were') . " applied.\n", Console::FG_GREEN);
            $this->stdout("\nMigrated up successfully.\n", Console::FG_GREEN);
        }
    }


    /**
     * Migrate up
     * @param string $class
     * @return bool
     */
    protected function migrateUp($class)
    {
        $return = parent::migrateUp($class);
        if ($this->develop && $return) {
            // Save current cast
            $this->getCastController()->castDb->saveCurrentCast();
            $this->stdout("\n" . 'CastDb updated' . "\n", Console::FG_GREEN);
        }

        return $return;
    }


    /**
     * Get Cast controller object
     * @return CastController
     */
    protected function getCastController()
    {
        if (!isset($this->_cache['castController'])) {
            $this->_cache['castController'] = new CastController([
                'db'     => $this->db,
                'worker' => [
                    'type' => 'file',
                    'file' => \Yii::getAlias('@console/runtime/dbCast.json'),
                ]
            ]);
        }
        /** @var CastController $castController */
        $castController = $this->_cache['castController'];
        $castController->castDb->resetCache();

        return $castController;
    }


    /**
     * Make presentation for array data
     * @param $dataArray
     */
    protected function makeArrayPresentation($dataArray)
    {
        $this->stdout(json_encode($dataArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n");
    }

}