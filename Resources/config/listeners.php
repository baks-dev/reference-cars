<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Reference\Cars\Entity;
use BaksDev\Reference\Cars\EntityListeners;

return static function (ContainerConfigurator $configurator)
{
    $services = $configurator->services()
      ->defaults()
      ->autowire()
      ->autoconfigure();
    
    /** EntityListeners */
    $services->set(EntityListeners\Brand\CarsBrandModifyListener::class)
      ->class(EntityListeners\Brand\CarsBrandModifyListener::class)
      ->tag(
        'doctrine.orm.entity_listener',
        ['event' => 'prePersist', 'lazy' => true, 'entity' => Entity\Brand\Modify\CarsBrandModify::class]);
	
	
	$services->set(EntityListeners\Model\CarsBrandListener::class.'prePersist')
		->class(EntityListeners\Model\CarsBrandListener::class)
		->tag(
			'doctrine.orm.entity_listener',
			['event' => 'prePersist', 'lazy' => true, 'entity' => Entity\Brand\CarsBrand::class]);
	
	
	$services->set(EntityListeners\Model\CarsBrandListener::class.'preUpdate')
		->class(EntityListeners\Model\CarsBrandListener::class)
		->tag(
			'doctrine.orm.entity_listener',
			['event' => 'preUpdate', 'lazy' => true, 'entity' => Entity\Brand\CarsBrand::class]);
	
	
	
	
	
};
