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

namespace BaksDev\Reference\Cars\Entity\Brand\Event;


use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Logo\CarsBrandLogo;
use BaksDev\Reference\Cars\Entity\Brand\Modify\CarsBrandModify;
use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;
use BaksDev\Reference\Cars\Type\Brand\Event\CarsBrandEventUid;

use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Entity\EntityState;
//use BaksDev\Core\Type\Locale\Locale;
//use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Core\Type\Modify\ModifyActionEnum;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'cars_brand_event')]
//#[ORM\Index(columns: ['profile'])]
class CarsBrandEvent extends EntityEvent
{
	public const TABLE = 'cars_brand_event';
	
	/** ID */
	#[ORM\Id]
	#[ORM\Column(type: CarsBrandEventUid::TYPE)]
	private CarsBrandEventUid $id;
	
	/** ID Profile */
	#[ORM\Column(type: CarsBrandUid::TYPE, nullable: false)]
	private ?CarsBrandUid $brand = null;
	
	/** Аватарка профиля */
	#[ORM\OneToOne(mappedBy: 'event', targetEntity: CarsBrandLogo::class, cascade: ['all'])]
	private ?CarsBrandLogo $logo = null;
	
	/** Модификатор */
	#[ORM\OneToOne(mappedBy: 'event', targetEntity: CarsBrandModify::class, cascade: ['all'])]
	private CarsBrandModify $modify;
	
	/** Перевод */
	#[ORM\OneToMany(mappedBy: 'event', targetEntity: CarsBrandTrans::class, cascade: ['all'])]
	private Collection $translate;
	
	public function __toString() : string
	{
		return $this->id;
	}
	
	public function __construct()
	{
		$this->id = new CarsBrandEventUid();
		$this->modify = new CarsBrandModify($this, new ModifyAction(ModifyActionEnum::NEW));
		
	}
	
	public function __clone()
	{
		$this->id = new CarsBrandEventUid();
	}
	

	public function getId() : CarsBrandEventUid
	{
		return $this->id;
	}
	
	public function setBrand(CarsBrandUid|CarsBrand $brand) : void
	{
		$this->brand = $brand instanceof CarsBrand ? $brand->getId() : $brand;
	}
	

	public function getBrand() : ?CarsBrandUid
	{
		return $this->brand;
	}
	
	/**
	 * @throws Exception
	 */
	public function getDto($dto) : mixed
	{
		if($dto instanceof CarsBrandEventInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	/**
	 * @throws Exception
	 */
	public function setEntity($dto) : mixed
	{
		if($dto instanceof CarsBrandEventInterface)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	
	public function isModifyActionEquals(ModifyActionEnum $action) : bool
	{
		return $this->modify->equals($action);
	}
	
	public function getUploadLogo() : CarsBrandLogo
	{
		return $this->logo ?: $this->logo = new CarsBrandLogo($this);
	}
	
	public function getNameByLocale(Locale $locale) : ?string
	{
		$name = null;
		
		/** @var CarsBrandTrans $trans */
		foreach($this->translate as $trans)
		{
			if($name = $trans->name($locale))
			{
				break;
			}
		}
		
		return $name;
	}
}