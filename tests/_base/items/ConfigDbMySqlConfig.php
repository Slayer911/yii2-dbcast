<?php


namespace DBCast\tests\_base\items;


use DBCast\helpers\ConsoleHelper;
use DBCast\tests\_base\ConfigCastBase;
use DBCast\tests\_base\interfaces\ConfigDbInterface;
use yii\db\Connection;

/**
 * Class ConfigDbMySqlConfig
 * @package DBCast\tests\_base
 */
class ConfigDbMySqlConfig extends ConfigCastBase implements ConfigDbInterface
{

    /**
     * Cache
     * @var array
     */
    protected $_cache = [];

    /**
     * Base config name
     * @var
     */
    protected $baseConfigName = 'mysqlConfig';

    /**
     * ConfigDbMySqlConfig constructor.
     * @param null $configName
     */
    public function __construct($configName = null)
    {
        parent::__construct($configName);
    }

    /**
     * Restore DB
     */
    protected function restore($restoreFile)
    {
        ConsoleHelper::log('Restore MySql ' . $restoreFile, 3);

        $dnsMysqlConfig = $this->parseMySqlDns();

        // Delete previous tables
        $this->getConnectionObject()->createCommand('DROP DATABASE ' . $dnsMysqlConfig['dbname'])->execute();
        $this->getConnectionObject()->createCommand('CREATE DATABASE ' . $dnsMysqlConfig['dbname'])->execute();

        // Restore
        $command = 'mysql -u ' . $this->config['username'];
        if (!empty($this->config['password'])) {
            $command .= ' -p ' . $this->config['password'];
        }
        $command .= ' -h ' . $dnsMysqlConfig['host'] . ' ' . $dnsMysqlConfig['dbname'] . ' < ' . $restoreFile;
        exec($command);
    }

    /**
     * Parse MySql dns
     * @return array
     */
    protected function parseMySqlDns()
    {
        $dns    = $this->config['dsn'];
        $result = [];
        if (preg_match_all('/(?<=(:|;))([-a-z0-9_]+)=([-a-z0-9_]+)($|;)/', $dns, $matches)) {
            foreach ($matches[2] as $key => $value) {
                $result[$value] = $matches[3][$key];
            }
        }

        return $result;
    }

    /**
     * Get connection object
     * @return Connection|object
     */
    protected function getConnectionObject()
    {
        if (!isset($this->_cache['connectionObject'])) {
            $this->_cache['connectionObject'] = \Yii::createObject($this->getConfig());
        }

        return $this->_cache['connectionObject'];
    }

}