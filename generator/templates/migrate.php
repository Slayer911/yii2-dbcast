<?php
/**
 * This is the template for migrate
 * @var       $this          yii\web\View
 * @var       $migrationName string migration name
 * @var array $lines
 * @var array $linesReverse
 */
echo "<?php" . PHP_EOL . PHP_EOL;


echo 'class ' . $migrationName . ' extends \yii\db\Migration' . PHP_EOL . '{' . PHP_EOL . PHP_EOL;

echo '    public function up()' . PHP_EOL;
echo '    {' . PHP_EOL;

foreach ($lines as $line) {
    echo '        ' . $line . PHP_EOL;
}
echo '    }' . PHP_EOL . PHP_EOL;


echo '    public function down()' . PHP_EOL;
echo '    {' . PHP_EOL;
foreach ($linesReverse as $line) {
    echo '        ' . $line . PHP_EOL;
}
echo '    }' . PHP_EOL . PHP_EOL;


echo '}';