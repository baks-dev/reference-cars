<?php

use BaksDev\Reference\Cars\BaksDevReferenceCarsBundle;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function(RoutingConfigurator $routes) {

    $MODULE = BaksDevReferenceCarsBundle::PATH;

    $routes->import(
        $MODULE.'Controller',
        'attribute',
        false,
        $MODULE.implode(DIRECTORY_SEPARATOR, ['Controller', '**', '*Test.php'])
    )
        ->prefix(\BaksDev\Core\Type\Locale\Locale::routes())
        ->namePrefix('reference-cars:');
};
