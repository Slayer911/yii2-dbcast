<?php
/** @var string $dataDir */
require('_bootstrap.php');
return [
    'type'           => 'file',
    'file'           => $dataDir . 'dbcast.json',
    '_restore-old' => $dataDir . 'dbcast-template-old.json',
    '_restore-new' => $dataDir . 'dbcast-template-new.json'
];
