# BaksDev Reference Cars

[![Version](https://img.shields.io/badge/version-7.0.24-blue)](https://github.com/baks-dev/reference-cars/releases)
![php 8.2+](https://img.shields.io/badge/php-min%208.1-red.svg)

Библиотека автомобилей (Бренды, модели, параметры)

## Установка

``` bash
$ composer require baks-dev/reference-cars
```

## Настройки

Для отображения в выпадающих списках, добавить настройку сервиса в конфиг:

``` php
<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Reference\Clothing\Choice\ReferenceChoiceSizeClothing;

return static function (ContainerConfigurator $configurator) {
	
	$services = $configurator->services()
	    ->defaults()
	    ->autowire(true)
	    ->autoconfigure(true)
	;

	$services
	    ->set(ReferenceChoiceCars::class)
	    ->tag('baks.reference.choice')
	;
};

```

## Журнал изменений ![Changelog](https://img.shields.io/badge/changelog-yellow)

О том, что изменилось за последнее время, обратитесь к [CHANGELOG](CHANGELOG.md) за дополнительной информацией.

## Лицензия ![License](https://img.shields.io/badge/MIT-green)

The MIT License (MIT). Обратитесь к [Файлу лицензии](LICENSE.md) за дополнительной информацией.
