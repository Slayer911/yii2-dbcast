Yii2 DbCast
=================
Auto generate migration by your changes DB schema.
Module create cast of previous DB schema, and current. By the different - 
system create Yii migrate on OOP style.

Currently supports only **MySql**

Install
-------
```
composer config repositories.dbCast git https://github.com/Slayer911/yii2-dbcast.git
composer require --dev "slayer911/yii2-dbcast:*"
```
After vendor was installed - create first DBCast.
```
 php yii migrate/cast-save
```
If in this step you have error - add to console config (main-local.php) next data:
```$xslt
<?php
return [
    ...
    'controllerMap' => [
        'migrate' => [
            'class'   => \DBCast\controllers\MigrateController::class,
            'develop' => true
        ]
    ]
    ...
];

```


Using
------
Default Yii controller was extended by DbCast Migrate controller.
Now you have next commands:
![alt text](newCommand.jpg) 
* migrate/cast-commit

Create new migration.
![alt text](castCommit.jpg) 


 




