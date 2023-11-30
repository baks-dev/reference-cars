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

namespace BaksDev\Reference\Cars\Entity\Brand;

use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use BaksDev\Reference\Cars\Type\Brand\Event\CarsBrandEventUid;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'cars_brand')]
class CarsBrand
{
    public const TABLE = 'cars_brand';
    
    /** ID */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Id]
    #[ORM\Column(type: CarsBrandUid::TYPE)]
    private CarsBrandUid $id;
	
	
	/** ID События */
    #[Assert\Uuid]
    #[Assert\NotBlank]
	#[ORM\Column(type: CarsBrandEventUid::TYPE, unique: true)]
	private CarsBrandEventUid $event;
	

	public function __construct(?CarsBrandUid $id = null)
	{
		$this->id = $id ?: new CarsBrandUid();
	}

    public function __toString(): string
    {
        return (string) $this->id;
    }
	
	public function getId() : CarsBrandUid
	{
		return $this->id;
	}
	
	public function getEvent() : CarsBrandEventUid
	{
		return $this->event;
	}
	
	
	public function setEvent(CarsBrandEventUid|CarsBrandEvent $event) : void
	{
		$this->event = $event instanceof CarsBrandEvent ? $event->getId() : $event;
	}
	
}