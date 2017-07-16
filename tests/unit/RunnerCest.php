<?php

use  \DBCast\tests\_unitUniversal\UniversalCastControllerConfigContract;

/**
 * Class RunnerCest
 */
class RunnerCest
{


    /**
     * Config objects
     * @var array
     */
    protected $configObjects = [
        'worker' => [
            'file' => null
        ],
        'castDB' => [
            'mysql' => null
        ]
    ];

    /**
     * Init
     */
    public function init()
    {
        $this->configObjects['worker']['file']  = new \DBCast\tests\_base\items\ConfigDataFileWorker();
        $this->configObjects['castDB']['mysql'] = new \DBCast\tests\_base\items\ConfigDbMySqlConfig();
    }


    /**
     * Get directory with universal tests
     * @return string
     */
    protected function getUniversalPath()
    {
        return __DIR__ . '/../_unitUniversal';
    }

    /**
     * Run
     * @param UnitTester $I
     */
    public function run(\UnitTester $I)
    {
        if ($files = $this->getAllUniversalFiles()) {
            foreach ($files as $filePath) {
                /**
                 * @var \DBCast\tests\_unitUniversal\_UniversalCestBase $universalObject
                 */
                $universalObject = $this->createObjectByFilePath($filePath, $this->configObjects['worker']['file'], $this->configObjects['castDB']['mysql']);
                $this->runAllTestMethods($universalObject, $I);
            }
        }
    }

    /**
     * Run all methods of test object
     * @param            $object
     * @param UnitTester $I
     */
    protected function runAllTestMethods($object, \UnitTester $I)
    {
        $methods = get_class_methods($object);
        foreach ($methods as $methodName) {
            if(!preg_match('/^_/',$methodName)){
                \DBCast\helpers\ConsoleHelper::log($methodName,2);
                $object->{$methodName}($I);
            }
        }
    }


    /**
     * Create Object by universal Class
     * @param $filePath
     * @param $configWorker
     * @param $configCastDb
     * @return mixed
     */
    protected function createObjectByFilePath($filePath, $configWorker, $configCastDb)
    {
        if (preg_match('/(Universal[-_a-z0-9]+).php$/i', $filePath, $matches)) {
            $className = $matches[1];
            \DBCast\helpers\ConsoleHelper::log($className, 1);
            $className = '\DBCast\tests\_unitUniversal\\' . $matches[1];
            require_once($filePath);
            $universalObject = new $className($configWorker, $configCastDb);

            return $universalObject;
        }

        throw new LogicException($filePath . ' does not correct Universal test class');
    }

    /**
     * Get Universal test files
     * @return array
     */
    protected function getAllUniversalFiles()
    {
        $dir   = $this->getUniversalPath();
        $files = glob($dir . '/Universal*.php');

        return $files;
    }

}