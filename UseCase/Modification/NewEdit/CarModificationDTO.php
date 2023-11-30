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

namespace BaksDev\Reference\Cars\UseCase\Modification\NewEdit;

use BaksDev\Reference\Cars\Entity\Modification\Event\CarModificationEventInterface;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use BaksDev\Reference\Cars\Type\Modification\Event\CarsModificationEventUid;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

final class CarModificationDTO implements CarModificationEventInterface
{
	
	/** Идентификатор события */
	#[Assert\Uuid]
	private ?CarsModificationEventUid $id = null;
	
	/** Модификация */
	#[Assert\NotBlank]
	private string $modification;
	
	
	/** Характеристика модификации */
	#[Assert\Valid]
	private ArrayCollection $characteristic;

    #[Assert\Uuid]
    private ?CarsModelUid $model = null;

    #[Assert\Valid]
    private Info\CarsModificationInfoDTO $info;


    public function __construct(){
		//$this->characteristic = new Characteristic\CarsModificationCharacteristicsDTO();
		$this->characteristic = new ArrayCollection();
        $this->info = new Info\CarsModificationInfoDTO();
    }
	
	public function getEvent() : ?CarsModificationEventUid
	{
		return $this->id;
	}
	
	/** Модификация */
	public function getModification(): string
	{
		return $this->modification;
	}

	public function setModification(string $modification) : void
	{
		$this->modification = $modification;
	}

	
	
	/** Характеристика модификации */
	public function getCharacteristic() : ArrayCollection
	{
		return $this->characteristic;
	}
	
	public function addCharacteristic(Characteristic\CarsModificationCharacteristicsDTO $characteristic) : void
	{
		if(!$this->characteristic->contains($characteristic))
		{
			$this->characteristic->add($characteristic);
		}
	}
	
	
		public function removeCharacteristic(Characteristic\CarsModificationCharacteristicsDTO $characteristic) : void
	{
		$this->characteristic->removeElement($characteristic);
	}

    /**
     * Model
     */
    public function getModel(): ?CarsModelUid
    {
        return $this->model;
    }

    public function setModel(?CarsModelUid $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Info
     */
    public function getInfo(): Info\CarsModificationInfoDTO
    {
        return $this->info;
    }

    public function setInfo(Info\CarsModificationInfoDTO $info): self
    {
        $this->info = $info;
        return $this;
    }
}