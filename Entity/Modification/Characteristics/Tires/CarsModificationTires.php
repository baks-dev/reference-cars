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

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use BaksDev\Reference\Cars\Type\Modification\Tires\CarsModificationTiresUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* Перевод CarsModificationTires */

#[ORM\Entity]
#[ORM\Table(name: 'cars_modification_tires')]
class CarsModificationTires extends EntityEvent
{
	public const TABLE = 'cars_modification_tires';
	
	/** Идентификатор */
    #[Assert\Uuid]
    #[Assert\NotBlank]
	#[ORM\Id]
	#[ORM\Column(type: CarsModificationTiresUid::TYPE)]
	private CarsModificationTiresUid $id;
	
	/** Связь на характеристику */
    #[Assert\Uuid]
    #[Assert\NotBlank]
	#[ORM\ManyToOne(targetEntity: CarsModificationCharacteristics::class, inversedBy: "tire")]
	#[ORM\JoinColumn(name: 'characteristic', referencedColumnName: "id")]
	private CarsModificationCharacteristics $characteristic;
	
	/** Размер */
	#[ORM\Column(type: Types::SMALLINT, nullable: true)]
	private ?int $size = null;
	
	/** Профиль */
	#[ORM\Column(type: Types::SMALLINT, nullable: true)]
	private ?int $profile = null;
	
	/** Радиус */
	#[ORM\Column(type: Types::SMALLINT, nullable: true)]
	private ?int $radius = null;
	
	
	public function __construct(CarsModificationCharacteristics $characteristic)
	{
		$this->characteristic = $characteristic;
		$this->id = new CarsModificationTiresUid();
	}


    public function __toString(): string
    {
        return (string) $this->characteristic;
    }

	public function getDto($dto): mixed
	{
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

		if($dto instanceof CarsModificationTiresInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	public function setEntity($dto): mixed
	{
		
		if($dto instanceof CarsModificationTiresInterface || $dto instanceof self)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	

}