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

use BaksDev\Reference\Cars\Entity\Modification\CarsModification;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use BaksDev\Reference\Cars\Entity\Modification\Modify\CarsModificationModify;
use BaksDev\Reference\Cars\Type\Modification\Event\CarsModificationEventUid;
use BaksDev\Reference\Cars\Type\Modification\Id\CarsModificationUid;
use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Type\Modify\ModifyActionEnum;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;


/* CarModificationEvent */

#[ORM\Entity]
#[ORM\Table(name: 'cars_modification_event')]
#[ORM\Index(columns: ['modification'])]
class CarsModificationEvent extends EntityEvent
{
	public const TABLE = 'cars_modification_event';
	
	/** ID */
	#[ORM\Id]
	#[ORM\Column(type: CarsModificationEventUid::TYPE)]
	private CarsModificationEventUid $id;
	
	/** ID CarModification */
	#[ORM\Column(type: CarsModificationUid::TYPE)]
	private ?CarsModificationUid $main = null;
	
	/** Модификация */
	#[ORM\Column(type: Types::STRING)]
	private string $modification;
	
	/** Характеристика модификации модели */
	#[ORM\OneToMany(mappedBy: 'event', targetEntity: CarsModificationCharacteristics::class, cascade: ['all'])]
	private Collection $characteristic;
	
	/** Модификатор */
	#[ORM\OneToOne(mappedBy: 'event', targetEntity: CarsModificationModify::class, cascade: ['all'])]
	private CarsModificationModify $modify;
	
	
	public function __toString() : string
	{
		return $this->id;
	}
	
	public function __construct()
	{
		$this->id = new CarsModificationEventUid();
		$this->modify = new CarsModificationModify($this);
	
	}
	
	public function __clone()
	{
		$this->id = new CarsModificationEventUid();
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
	

	public function getDto($dto) : mixed
	{
		if($dto instanceof CarModificationEventInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	

	public function setEntity($dto) : mixed
	{
		if($dto instanceof CarModificationEventInterface)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	
	public function isModifyActionEquals(ModifyActionEnum $action) : bool
	{
		return $this->modify->equals($action);
	}
	
	//	public function getUploadClass() : CarModificationImage
	//	{
	//		return $this->image ?: $this->image = new CarModificationImage($this);
	//	}
	
	//	public function getNameByLocale(Locale $locale) : ?string
	//	{
	//		$name = null;
	//		
	//		/** @var CarModificationTrans $trans */
	//		foreach($this->translate as $trans)
	//		{
	//			if($name = $trans->name($locale))
	//			{
	//				break;
	//			}
	//		}
	//		
	//		return $name;
	//	}
}