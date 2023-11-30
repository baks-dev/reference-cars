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

namespace BaksDev\Reference\Cars\Entity\Modification\Event;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Type\Modify\Modify\ModifyActionNew;
use BaksDev\Core\Type\Modify\Modify\ModifyActionUpdate;
use BaksDev\Reference\Cars\Entity\Modification\CarsModification;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use BaksDev\Reference\Cars\Entity\Modification\Info\CarsModificationInfo;
use BaksDev\Reference\Cars\Entity\Modification\Modify\CarsModificationModify;
use BaksDev\Reference\Cars\Type\Modification\Event\CarsModificationEventUid;
use BaksDev\Reference\Cars\Type\Modification\Id\CarsModificationUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;


/* CarModificationEvent */

#[ORM\Entity]
#[ORM\Table(name: 'cars_modification_event')]
#[ORM\Index(columns: ['modification'])]
class CarsModificationEvent extends EntityEvent
{
	public const TABLE = 'cars_modification_event';
	
	/** ID */
	#[Assert\Uuid]
	#[Assert\NotBlank]
	#[ORM\Id]
	#[ORM\Column(type: CarsModificationEventUid::TYPE)]
	private CarsModificationEventUid $id;
	
	/** ID CarModification */
    #[Assert\Uuid]
    #[Assert\NotBlank]
	#[ORM\Column(type: CarsModificationUid::TYPE)]
	private ?CarsModificationUid $main = null;
	
	/** Модификация */
    #[Assert\NotBlank]
	#[ORM\Column(type: Types::STRING)]
	private string $modification;
	
	/** Характеристика модификации модели */
    #[Assert\Valid]
	#[ORM\OneToMany(mappedBy: 'event', targetEntity: CarsModificationCharacteristics::class, cascade: ['all'])]
	private Collection $characteristic;
	
	/** Модификатор события */
    #[Assert\Valid]
	#[ORM\OneToOne(mappedBy: 'event', targetEntity: CarsModificationModify::class, cascade: ['all'])]
	private CarsModificationModify $modify;


    /** Информация о модификации */
    #[ORM\OneToOne(mappedBy: 'event', targetEntity: CarsModificationInfo::class, cascade: ['all'])]
    private ?CarsModificationInfo $info = null;


	public function __construct()
	{
		$this->id = new CarsModificationEventUid();
		$this->modify = new CarsModificationModify($this);
	
	}
	
	public function __clone()
	{
        $this->id = clone $this->id;
	}

    public function __toString(): string
    {
        return (string) $this->id;
    }
	
	
	public function getId() : CarsModificationEventUid
	{
		return $this->id;
	}
	
	public function setMain(CarsModificationUid|CarsModification $main) : void
	{
		$this->main = $main instanceof CarsModification ? $main->getId() : $main;
	}
	
	
	public function getMain() : ?CarsModificationUid
	{
		return $this->main;
	}
	

	public function getDto($dto): mixed
	{
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

		if($dto instanceof CarModificationEventInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	

	public function setEntity($dto): mixed
	{
		if($dto instanceof CarModificationEventInterface || $dto instanceof self)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}

}