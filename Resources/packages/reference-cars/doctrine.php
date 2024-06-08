<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Reference\Cars\BaksDevReferenceCarsBundle;
use BaksDev\Reference\Cars\Type\Brand\Event\CarsBrandEventType;
use BaksDev\Reference\Cars\Type\Brand\Event\CarsBrandEventUid;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandType;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use BaksDev\Reference\Cars\Type\Model\Event\CarsModelEventType;
use BaksDev\Reference\Cars\Type\Model\Event\CarsModelEventUid;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelType;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use BaksDev\Reference\Cars\Type\Model\Type\CarsModelClass;
use BaksDev\Reference\Cars\Type\Model\Type\CarsModelClassType;
use BaksDev\Reference\Cars\Type\Modification\Characteris\CarsModificationCharacteristicsType;
use BaksDev\Reference\Cars\Type\Modification\Characteris\CarsModificationCharacteristicsUid;
use BaksDev\Reference\Cars\Type\Modification\Disc\CarsModificationDiscType;
use BaksDev\Reference\Cars\Type\Modification\Disc\CarsModificationDiscUid;
use BaksDev\Reference\Cars\Type\Modification\Event\CarsModificationEventType;
use BaksDev\Reference\Cars\Type\Modification\Event\CarsModificationEventUid;
use BaksDev\Reference\Cars\Type\Modification\Id\CarsModificationType;
use BaksDev\Reference\Cars\Type\Modification\Id\CarsModificationUid;
use BaksDev\Reference\Cars\Type\Modification\Tires\CarsModificationTiresType;
use BaksDev\Reference\Cars\Type\Modification\Tires\CarsModificationTiresUid;
use Symfony\Config\DoctrineConfig;

return static function(ContainerConfigurator $container, DoctrineConfig $doctrine): void {

    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $doctrine->dbal()->type(CarsBrandUid::TYPE)->class(CarsBrandType::class);
    $doctrine->dbal()->type(CarsBrandEventUid::TYPE)->class(CarsBrandEventType::class);

    $doctrine->dbal()->type(CarsModelUid::TYPE)->class(CarsModelType::class);
    $doctrine->dbal()->type(CarsModelEventUid::TYPE)->class(CarsModelEventType::class);
    $doctrine->dbal()->type(CarsModelClass::TYPE)->class(CarsModelClassType::class);

    $doctrine->dbal()->type(CarsModificationUid::TYPE)->class(CarsModificationType::class);
    $doctrine->dbal()->type(CarsModificationEventUid::TYPE)->class(CarsModificationEventType::class);
    $doctrine->dbal()->type(CarsModificationCharacteristicsUid::TYPE)
        ->class(CarsModificationCharacteristicsType::class);

    $doctrine->dbal()->type(CarsModificationDiscUid::TYPE)->class(CarsModificationDiscType::class);
    $doctrine->dbal()->type(CarsModificationTiresUid::TYPE)->class(CarsModificationTiresType::class);

    $services->set(CarsBrandUid::class)->class(CarsBrandUid::class);
    $services->set(CarsModelUid::class)->class(CarsModelUid::class);


    $emDefault = $doctrine->orm()->entityManager('default')->autoMapping(true);

    $emDefault->mapping('reference-cars')
        ->type('attribute')
        ->dir(BaksDevReferenceCarsBundle::PATH.'Entity')
        ->isBundle(false)
        ->prefix('BaksDev\Reference\Cars\Entity')
        ->alias('reference-cars');
};
