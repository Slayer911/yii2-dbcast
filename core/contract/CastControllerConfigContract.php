<?php

namespace DBCast\core\contract;


/**
 * Class CastControllerConfigContract
 * config
 * [
 *  'db' => Connection,
 *  'worker' => [
 *      'type' => 'file',
 *      'file' => '/var/www/site/runtime/dbCast.json'
 *  ]
 * ]
 */
class CastControllerConfigContract
{

    /**
     * @var array
     */
    protected $config;

    /**
     * CastControllerConfigContract constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->check();
    }

    /**
     * Check config array
     */
    public function check()
    {
        if (empty($this->config['worker']['type'])) {
            throw new \InvalidArgumentException('Attribute $config[\'worker\'][\'type\'] must be filled');
        }
        if (empty($this->config['db'])) {
            throw new \InvalidArgumentException('Attribute $config[\'db\'] must be filled');
        }
    }


    /**
     * Get worker config
     * @return array
     */
    public function getWorkerConfig()
    {
        return $this->config['worker'];
    }


    /**
     * Get DB config
     * @return mixed
     */
    public function getDbConfig()
    {
        return $this->config['db'];
    }

}