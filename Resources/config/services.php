<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $configurator){
	$services = $configurator->services()
		->defaults()
		->autowire()      // Automatically injects dependencies in your services.
		->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
	;
	
	
	$namespace = 'BaksDev\Reference\Cars';
	

	$services->load($namespace.'\Controller\\', '../../Controller')
		->tag('controller.service_arguments')
        ->exclude(__DIR__.'/../../Controller/**/*Test.php')
	;
	
	//$services->load($namespace.'\Repository\\', '../../Repository');
	
	//    $services->load('App\Module\Users\Profile\TypeProfile\Handler\\', '../../Handler')
	//      ->exclude('../../Handler/**/*DTO.php');
	
	//$services->load($namespace.'\DataFixtures\\', '../../DataFixtures');
	
	$services->load($namespace.'\UseCase\\', '../../UseCase')
        ->exclude(__DIR__.'/../../UseCase/**/{*DTO.php,*Test.php}')
	;
};

