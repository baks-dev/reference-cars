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

namespace BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit;

use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEventInterface;
use BaksDev\Reference\Cars\Type\Model\Event\CarsModelEventUid;
use BaksDev\Reference\Cars\Type\Model\Type\CarsModelClassEnum;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\Image\CarsModelImageDTO;
use BaksDev\Core\Type\Locale\Locale;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

final class CarsModelDTO implements CarsModelEventInterface
{
	/** Идентификатор события */
	#[Assert\Uuid]
	private ?CarsModelEventUid $id = null;
	
	private CarsModelClassEnum $class;
	
	/** Код автомобиля  */
	private ?string $code = null;
	
	/** Год начала выпуска авто  */
	private int $from;
	
	/** Год окончания выпуска авто (null - по наст. время) */
	private ?int $to = null;
	
	
	/** Обложка модели */
	#[Assert\Valid]
	private CarsModelImageDTO $image;
	
	/** Перевод */
	#[Assert\Valid]
	private ArrayCollection $translate;
	
	public function __construct()
	{
		$this->translate = new ArrayCollection();
		$this->image = new Image\CarsModelImageDTO();
	}
	
	
	public function getEvent() : ?CarsModelEventUid
	{
		return $this->id;
	}
	
	
	/* CLASS */
	
	public function getClass() : CarsModelClassEnum
	{
		return $this->class;
	}

	public function setClass(CarsModelClassEnum $class) : void
	{
		$this->class = $class;
	}
	
	
	/* IMAGE */
	
	public function getImage() : Image\CarsModelImageDTO
	{
		return $this->image;
	}
	
	public function setImage(Image\CarsModelImageDTO $image) : void
	{
		$this->image = $image;
	}
	
	
	/* TRANSLATE */
	
	public function getTranslate() : ArrayCollection
	{
		/* Вычисляем расхождение и добавляем неопределенные локали */
		foreach(Locale::diffLocale($this->translate) as $locale)
		{
			$TransFormDTO = new Trans\CarsModelTransDTO();
			$TransFormDTO->setLocal($locale);
			$this->addTranslate($TransFormDTO);
		}
		
		return $this->translate;
	}
	
	public function addTranslate(Trans\CarsModelTransDTO $translate) : void
	{
		$this->translate->add($translate);
	}
	
	public function removeTranslate(Trans\CarsModelTransDTO $translate) : void
	{
		$this->translate->removeElement($translate);
	}
	
	/**
	 * @return string|null
	 */
	public function getCode() : ?string
	{
		return $this->code;
	}
	
	/**
	 * @param string|null $code
	 */
	public function setCode(?string $code) : void
	{
		$this->code = $code;
	}
	
	/**
	 * @return int
	 */
	public function getFrom() : int
	{
		return $this->from;
	}
	
	/**
	 * @param int $from
	 */
	public function setFrom(int $from) : void
	{
		$this->from = $from;
	}
	
	/**
	 * @return int|null
	 */
	public function getTo() : ?int
	{
		return $this->to;
	}
	
	/**
	 * @param int|null $to
	 */
	public function setTo(?int $to) : void
	{
		$this->to = $to;
	}
	
	
}