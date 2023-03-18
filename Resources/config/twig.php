<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Module\Users\Profile\UserProfile\Type\Status\UserProfileStatusExtension;
use BaksDev\Reference\Cars\Entity\Brand\Logo\CarsBrandLogo;
use BaksDev\Reference\Cars\Entity\Model\Image\CarsModelImage;
use Symfony\Config\TwigConfig;

return static function (TwigConfig $config, ContainerConfigurator $configurator)
{
	$config->path(__DIR__.'/../view', 'Cars');
	
	
	
	
	/** Логотипы авто */
	
	/* Абсолютный Путь для загрузки логотипов авто */
	$configurator->parameters()->set(CarsBrandLogo::TABLE,
		'%kernel.project_dir%/public/upload/'.CarsBrandLogo::TABLE.'/'
	);
	
	/* Относительный путь логотипов авто */
	$config->global(CarsBrandLogo::TABLE)->value('/upload/'.CarsBrandLogo::TABLE.'/');
	
	
	
	
	
	/** Модели авто */
	
	/* Абсолютный Путь для загрузки моделей авто */
	$configurator->parameters()->set(CarsModelImage::TABLE,
		'%kernel.project_dir%/public/upload/'.CarsModelImage::TABLE.'/'
	);
	
	/* Относительный путь моделей авто */
	$config->global(CarsModelImage::TABLE)->value('/upload/'.CarsModelImage::TABLE.'/');

};




