<?php
/*
 * Copyright (c) 2023.  Baks.dev <admin@baks.dev>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace BaksDev\Reference\Cars\Entity\Modification\Characteristics\Tires;

use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use BaksDev\Reference\Cars\Type\Modification\Tires\CarsModificationTiresUid;
use BaksDev\Core\Entity\EntityEvent;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* Перевод CarsModificationTires */

#[ORM\Entity]
#[ORM\Table(name: 'cars_modification_tires')]
class CarsModificationTires extends EntityEvent
{
	public const TABLE = 'cars_modification_tires';
	
	/** Идентификатор */
	#[ORM\Id]
	#[ORM\Column(type: CarsModificationTiresUid::TYPE)]
	private readonly CarsModificationTiresUid $id;
	
	/** Связь на характеристику */
	#[ORM\ManyToOne(targetEntity: CarsModificationCharacteristics::class, inversedBy: "tire")]
	#[ORM\JoinColumn(name: 'characteristic', referencedColumnName: "id")]
	private readonly CarsModificationCharacteristics $characteristic;
	
	/** Размер */
	#[ORM\Column(type: Types::SMALLINT, length: 3, nullable: true)]
	private ?int $size = null;
	
	/** Профиль */
	#[ORM\Column(type: Types::SMALLINT, length: 3, nullable: true)]
	private ?int $profile = null;
	
	/** Радиус */
	#[ORM\Column(type: Types::SMALLINT, length: 3, nullable: true)]
	private ?int $radius = null;
	
	
	public function __construct(CarsModificationCharacteristics $characteristic)
	{
		$this->characteristic = $characteristic;
		$this->id = new CarsModificationTiresUid();
	}
	
	public function getDto($dto) : mixed
	{
		if($dto instanceof CarsModificationTiresInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	public function setEntity($dto) : mixed
	{
		
		if($dto instanceof CarsModificationTiresInterface)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	

}