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

namespace BaksDev\Reference\Cars\Entity\Modification\Characteristics;


use BaksDev\Reference\Cars\Entity\Modification\Event\CarsModificationEvent;
use BaksDev\Reference\Cars\Type\Modification\Characteris\CarsModificationCharacteristicsUid;
use BaksDev\Core\Entity\EntityEvent;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* CarsModificationCharacteristics */

#[ORM\Entity]
#[ORM\Table(name: 'cars_modification_characteristics')]
#[ORM\Index(columns: ['model'])]
class CarsModificationCharacteristics extends EntityEvent
{
	public const TABLE = 'cars_modification_characteristics';
	
	/** Идентификатор */
	#[ORM\Id]
	#[ORM\Column(type: CarsModificationCharacteristicsUid::TYPE)]
	private CarsModificationCharacteristicsUid $id;
	
	/** ID события */
	#[ORM\ManyToOne(targetEntity: CarsModificationEvent::class, inversedBy: "characteristic")]
	#[ORM\JoinColumn(name: 'event', referencedColumnName: "id")]
	private CarsModificationEvent $event;
	
	
	/** Модель двигателя */
	#[ORM\Column(type: Types::STRING, length: 256, nullable: true)]
	private string $model;
	
	/* Даты выпуска модицикации с таким двигателем */
	
	/** Год начала выпуска модификации  */
	#[ORM\Column(name: 'year_from', type: Types::SMALLINT, length: 4, nullable: false)]
	private int $from;
	
	/** Год окончания выпуска модификации (null - по наст. время) */
	#[ORM\Column(name: 'year_to', type: Types::SMALLINT, length: 4, nullable: true)]
	private ?int $to = null;
	
	
	/** Двигатель */
	#[ORM\OneToOne(mappedBy: 'characteristic', targetEntity: Motor\CarsModificationMotor::class, cascade: ['all'])]
	private Motor\CarsModificationMotor $motor;
	
	/** Шаси */
	#[ORM\OneToOne(mappedBy: 'characteristic', targetEntity: Chassis\CarsModificationChassis::class, cascade: ['all'])]
	private Chassis\CarsModificationChassis $chassi;
	
	/* Подбор комплектующих */
	
	/** Шины */
	#[ORM\OneToMany(mappedBy: 'characteristic', targetEntity: Tires\CarsModificationTires::class, cascade: ['all'])]
	private Collection $tire;
	
	/** Диски */
	#[ORM\OneToMany(mappedBy: 'characteristic', targetEntity: Disc\CarsModificationDisc::class, cascade: ['all'])]
	private Collection $disc;
	
	
	public function __construct(CarsModificationEvent $event)
	{
		$this->event = $event;
		$this->id = new CarsModificationCharacteristicsUid();
	}
	
	public function __clone() : void
	{
		$this->id = new CarsModificationCharacteristicsUid();
	}
	
	
	public function getDto($dto) : mixed
	{
		if($dto instanceof CarsModificationCharacteristicsInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	public function setEntity($dto) : mixed
	{
		if($dto instanceof CarsModificationCharacteristicsInterface)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
}