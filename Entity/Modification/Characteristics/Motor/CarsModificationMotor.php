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

namespace BaksDev\Reference\Cars\Entity\Modification\Characteristics\Motor;


use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use App\Module\Users\User\Entity\User;
use App\Module\Users\User\Type\Id\UserUid;
use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Entity\EntityState;
use App\System\Type\Ip\IpAddress;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Core\Type\Modify\ModifyActionEnum;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* CarsModificationMotor */

#[ORM\Entity]
#[ORM\Table(name: 'cars_modification_motor')]
class CarsModificationMotor extends EntityEvent
{
	public const TABLE = 'cars_modification_motor';
	
	/** ID характеристики */
	#[ORM\Id]
	#[ORM\OneToOne(inversedBy: 'motor', targetEntity: CarsModificationCharacteristics::class)]
	#[ORM\JoinColumn(name: 'characteristic', referencedColumnName: 'id')]
	private readonly CarsModificationCharacteristics $characteristic;
	
	/** Тип двигателя (бензин/дизль ...) */
	#[ORM\Column(type: Types::STRING, length: 32, nullable: true)]
	private ?string $fuel = null;
	
	/** Объем двигателя (л) */
	#[ORM\Column(type: Types::STRING, length: 5, nullable: true)]
	private ?string $engine = null;
	
	/** Мощность двигателя, л.с. */
	#[ORM\Column(type: Types::INTEGER, length: 5, nullable: true)]
	private ?int $power;
	
	/** Привод */
	#[ORM\Column(type: Types::STRING, length: 32, nullable: true)]
	private ?string $drive;
	
	public function __construct(CarsModificationCharacteristics $characteristic){
		$this->characteristic = $characteristic;
	}
	
	public function getDto($dto) : mixed
	{
		if($dto instanceof CarsModificationMotorInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	public function setEntity($dto) : mixed
	{
		if($dto instanceof CarsModificationMotorInterface)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
}