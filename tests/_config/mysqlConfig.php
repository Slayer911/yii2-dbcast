<?php
/** @var string $dataDir */
require('_bootstrap.php');
return [
    'class'        => '\yii\db\Connection',
    'dsn'          => 'mysql:host=localhost;dbname=dbcast',
    'username'     => 'root',
    'password'     => '',
    'charset'      => 'utf8',
    '_restore-old' => $dataDir . 'dump-old.sql',
    '_restore-new' => $dataDir . 'dump-new.sql'
];
