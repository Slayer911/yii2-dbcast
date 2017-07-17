Yii2 DbCast
=================
Auto generate migration by your changes DB schema.
Module create cast of previous DB schema, and current.

Currently supports only **MySql**

Install
-------
```
composer require --dev "slayer911/yii2-dbcast:*"
```
After vendor was installed - create first DBCast.
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
Default Yii controller was extended by DbCast Migrate controller.
Now you have next commands:
![newcommands](https://user-images.githubusercontent.com/9704032/28248769-cc5c1cc4-6a52-11e7-825c-a1ff07e44eeb.jpg)
* migrate/cast-commit

![castcommit](https://user-images.githubusercontent.com/9704032/28248770-cc5d09b8-6a52-11e7-9575-52d18ee98aab.jpg) 
