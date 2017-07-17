<?php
namespace DBCast;

use yii\base\Application;
use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 * @package DBCast
 */
class Bootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        // Only console application
        if(is_a(\Yii::$app,\yii\console\Application::className())){
            // Hand config is a priority
            if(empty($app->controllerMap['migrate'])){
                $controllerMap                 = [
                        'migrate' => [
                            'class'   => \DBCast\controllers\MigrateController::class,
                            'develop' => true
                        ],
                    ] + $app->controllerMap;
                $app->controllerMap = $controllerMap;
            }
        }
    }
}
