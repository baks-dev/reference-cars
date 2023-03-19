<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Reference\Cars\Command\CarsBrandCommand;
use BaksDev\Reference\Cars\Command\CarsCharacteristicCommand;
use BaksDev\Reference\Cars\Command\CarsModelCommand;
use BaksDev\Reference\Cars\Command\CarsModificationCommand;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
      ->defaults()
      ->autowire()      // Automatically injects dependencies in your services.
      ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;
    
    $services->set('baks:cars:brand', CarsBrandCommand::class);
    $services->set('baks:cars:model', CarsModelCommand::class);
    $services->set('baks:cars:modification', CarsModificationCommand::class);
    $services->set('baks:cars:characteristics', CarsCharacteristicCommand::class);
	
};
