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

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* CarsModificationChassis */


#[ORM\Entity]
#[ORM\Table(name: 'cars_modification_chassis')]
class CarsModificationChassis extends EntityEvent
{
	public const TABLE = 'cars_modification_chassis';
	
	/** ID характеристики */
    #[Assert\Uuid]
    #[Assert\NotBlank]
	#[ORM\Id]
	#[ORM\OneToOne(inversedBy: 'chassis', targetEntity: CarsModificationCharacteristics::class)]
	#[ORM\JoinColumn(name: 'characteristic', referencedColumnName: 'id')]
	private CarsModificationCharacteristics $characteristic;
	
	/** Ступичное отверстие (DIA):*/
	#[ORM\Column(type: Types::STRING, nullable: true)]
	private ?string $dia = null;
	
	/** Диаметр (PCD):*/
	#[ORM\Column(type: Types::STRING, nullable: true)]
	private ?string $pcd = null;
	
	/** Количество крепежей */
	#[ORM\Column(type: Types::INTEGER, nullable: true)]
	private ?int $number = null;
	
	/** Крепёж */
	#[ORM\Column(type: Types::STRING, nullable: true)]
	private ?string $fastener = null;
	
	
	public function __construct(CarsModificationCharacteristics $characteristic)
	{
		$this->characteristic = $characteristic;
	}

    public function __toString(): string
    {
        return (string) $this->characteristic;
    }
	
	public function getDto($dto): mixed
	{
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

		if($dto instanceof CarsModificationChassisInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	
	public function setEntity($dto): mixed
	{
		if($dto instanceof CarsModificationChassisInterface || $dto instanceof self)
		{
			if(
				empty($dto->getPcd()) &&
				empty($dto->getDia()) &&
				empty($dto->getFastener()) &&
				empty($dto->getNumber())
			)
			{
				return false;
			}
			
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
}