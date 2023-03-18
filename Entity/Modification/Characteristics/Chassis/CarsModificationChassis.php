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

namespace BaksDev\Reference\Cars\Entity\Modification\Characteristics\Chassis;


use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use BaksDev\Core\Entity\EntityEvent;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* CarsModificationChassis */

#[ORM\Entity]
#[ORM\Table(name: 'cars_modification_chassis')]
class CarsModificationChassis extends EntityEvent
{
	public const TABLE = 'cars_modification_chassis';
	
	/** ID характеристики */
	#[ORM\Id]
	#[ORM\OneToOne(inversedBy: 'chassi', targetEntity: CarsModificationCharacteristics::class)]
	#[ORM\JoinColumn(name: 'characteristic', referencedColumnName: 'id')]
	private readonly CarsModificationCharacteristics $characteristic;
	
	
	/** Ступичное отверстие (DIA):*/
	#[ORM\Column(type: Types::STRING, length: 5, nullable: true)]
	private ?string $dia = null;
	
	/** Диаметр (PCD):*/
	#[ORM\Column(type: Types::STRING, length: 5, nullable: true)]
	private ?string $pcd = null;
	
	/** Количество крепежей */
	#[ORM\Column(type: Types::INTEGER, length: 2, nullable: true)]
	private ?int $number = null;
	
	/** Крепёж */
	#[ORM\Column(type: Types::STRING, length: 32, nullable: true)]
	private ?string $fastener = null;
	
	public function __construct(CarsModificationCharacteristics $characteristic){
		$this->characteristic = $characteristic;
	}
	
	public function getDto($dto) : mixed
	{
		if($dto instanceof CarsModificationChassisInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	public function setEntity($dto) : mixed
	{
		if($dto instanceof CarsModificationChassisInterface)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
}