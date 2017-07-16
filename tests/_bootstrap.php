<?php
$autoloadFIle = __DIR__ . '/../../../autoload.php';
// TODO change
$autoloadFIle = 'G:\OpenServer\domains\vendors\yii\advance\vendor\autoload.php';
$yiiFile = __DIR__ . '/../../../yiisoft/yii2/Yii.php';
$yiiFile = 'G:\OpenServer\domains\vendors\yii\advance\vendor\yiisoft\yii2\Yii.php';
if (!file_exists($autoloadFIle)) {
    die('Test must be run as vendor of yii project');
}
require_once $autoloadFIle;
require($yiiFile);

function d($data, $die = true)
{
    \yii\helpers\VarDumper::dump($data);
    if ($die) {
        die();
    }
}