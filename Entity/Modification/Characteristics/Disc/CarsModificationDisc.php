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

namespace BaksDev\Reference\Cars\Entity\Modification\Characteristics\Disc;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use BaksDev\Reference\Cars\Type\Modification\Disc\CarsModificationDiscUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* Перевод CarsModificationDisc */

#[ORM\Entity]
#[ORM\Table(name: 'cars_modification_disc')]
class CarsModificationDisc extends EntityEvent
{
	public const TABLE = 'cars_modification_disc';
	
	/** Идентификатор */
    #[Assert\Uuid]
    #[Assert\NotBlank]
	#[ORM\Id]
	#[ORM\Column(type: CarsModificationDiscUid::TYPE)]
	private CarsModificationDiscUid $id;
	
	/** Связь на характеристику */
    #[Assert\Uuid]
    #[Assert\NotBlank]
	#[ORM\ManyToOne(targetEntity: CarsModificationCharacteristics::class, inversedBy: "disc")]
	#[ORM\JoinColumn(name: 'characteristic', referencedColumnName: "id")]
	private CarsModificationCharacteristics $characteristic;
	
	/** Вылет (ET) */
	#[ORM\Column(type: Types::STRING,  nullable: true)]
	private ?string $et = null;
	
	/* Размер 9.5x18 */
	
	/** Диаметр 18″ */
	#[ORM\Column(type: Types::STRING, nullable: true)]
	private ?string $diameter = null;
	
	/** Ширина 9.5″ */
	#[ORM\Column(type: Types::STRING, nullable: true)]
	private ?string $width = null;
	
	
	public function __construct(CarsModificationCharacteristics $characteristic)
	{
		$this->characteristic = $characteristic;
		$this->id = new CarsModificationDiscUid();
	}

    public function __toString(): string
    {
        return (string) $this->characteristic;
    }

	public function getDto($dto): mixed
	{
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

		if($dto instanceof CarsModificationDiscInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	public function setEntity($dto): mixed
	{
		if($dto instanceof CarsModificationDiscInterface || $dto instanceof self)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
}