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

namespace BaksDev\Reference\Cars\Entity\Model\Event;

use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Model\Image\CarsModelImage;
use BaksDev\Reference\Cars\Entity\Model\Modify\CarsModelModify;
use BaksDev\Reference\Cars\Entity\Model\Trans\CarsModelTrans;
use BaksDev\Reference\Cars\Type\Model\Event\CarsModelEventUid;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use BaksDev\Reference\Cars\Type\Model\Type\CarsModelClassEnum;
use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Core\Type\Modify\ModifyActionEnum;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Exception;
use InvalidArgumentException;


/* CarsModelEvent */

#[ORM\Entity]
#[ORM\Table(name: 'cars_model_event')]
class CarsModelEvent extends EntityEvent
{
	public const TABLE = 'cars_model_event';
	
	/** ID */
	#[ORM\Id]
	#[ORM\Column(type: CarsModelEventUid::TYPE)]
	private CarsModelEventUid $id;
	
	/** ID CarsModel */
	#[ORM\Column(type: CarsModelUid::TYPE, nullable: false)]
	private ?CarsModelUid $main = null;
	
	/** Класс автомобиля  */
	#[ORM\Column(type: CarsModelClassEnum::TYPE, nullable: false)]
	private CarsModelClassEnum $class;
	
	/** Код автомобиля  */
	#[ORM\Column(type: Types::STRING, nullable: true)]
	private ?string $code = null;
	
	/** Год начала выпуска авто  */
	#[ORM\Column(name: 'year_from', type: Types::SMALLINT, length: 4, nullable: false)]
	private int $from;
	
	/** Год окончания выпуска авто (null - по наст. время) */
	#[ORM\Column(name: 'year_to', type: Types::SMALLINT, length: 4, nullable: true)]
	private ?int $to = null;
	
	
	/** Обложка модели */
	#[ORM\OneToOne(mappedBy: 'event', targetEntity: CarsModelImage::class, cascade: ['all'])]
	private ?CarsModelImage $image = null;
	
	/** Модификатор */
	#[ORM\OneToOne(mappedBy: 'event', targetEntity: CarsModelModify::class, cascade: ['all'])]
	private CarsModelModify $modify;
	
	/** Перевод */
	#[ORM\OneToMany(mappedBy: 'event', targetEntity: CarsModelTrans::class, cascade: ['all'])]
	private Collection $translate;
	
	public function __toString() : string
	{
		return $this->id;
	}
	
	public function __construct()
	{
		$this->id = new CarsModelEventUid();
		$this->modify = new CarsModelModify($this);
	}
	
	public function __clone()
	{
		$this->id = new CarsModelEventUid();
	}
	
	
	public function getId() : CarsModelEventUid
	{
		return $this->id;
	}
	
	public function setMain(CarsModelUid|CarsModel $main) : void
	{
		$this->main = $main instanceof CarsModel ? $main->getId() : $main;
	}
	
	
	public function getMain() : ?CarsModelUid
	{
		return $this->main;
	}
	
	/**
	 * @throws Exception
	 */
	public function getDto($dto) : mixed
	{
		if($dto instanceof CarsModelEventInterface)
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
		if($dto instanceof CarsModelEventInterface)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	
	public function isModifyActionEquals(ModifyActionEnum $action) : bool
	{
		return $this->modify->equals($action);
	}
	
	public function getUploadClass() : CarsModelImage
	{
		return $this->image ?: $this->image = new CarsModelImage($this);
	}
	
	public function getNameByLocale(Locale $locale) : ?string
	{
		$name = null;
		
		/** @var CarsModelTrans $trans */
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