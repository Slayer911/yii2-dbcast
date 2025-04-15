Yii2 DbCast
=================
Automatic generation of migration when changing the database schema. The module creates a snapshot of the previous database schema and the current one.

Currently, supports only **MySql**

Install
-------
```
composer require --dev "slayer911/yii2-dbcast:*"
```
After installing the vendor, create the first DBCast.
```
 php yii migrate/cast-save
```
If in this step you have an error - add to console config (main-local.php) next data:
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
The standard Yii controller has been extended with the DbCast Migrate controller.
You now have the following commands:
![newcommands](https://user-images.githubusercontent.com/9704032/28248769-cc5c1cc4-6a52-11e7-825c-a1ff07e44eeb.jpg)
* migrate/cast-commit

![castcommit](https://user-images.githubusercontent.com/9704032/28248770-cc5d09b8-6a52-11e7-9575-52d18ee98aab.jpg) 
