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

namespace BaksDev\Reference\Cars\Entity\Modification;

use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Modification\Event\CarsModificationEvent;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use BaksDev\Reference\Cars\Type\Modification\Event\CarsModificationEventUid;
use BaksDev\Reference\Cars\Type\Modification\Id\CarsModificationUid;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;


/* CarModification */

#[ORM\Entity]
#[ORM\Table(name: 'cars_modification')]
#[ORM\Index(columns: ['model'])]
class CarsModification
{
	public const TABLE = 'cars_modification';
	
	/** ID */
    #[Assert\Uuid]
    #[Assert\NotBlank]
	#[ORM\Id]
	#[ORM\Column(type: CarsModificationUid::TYPE)]
	private CarsModificationUid $id;
	
	/** ID События */
    #[Assert\Uuid]
    #[Assert\NotBlank]
	#[ORM\Column(type: CarsModificationEventUid::TYPE, unique: true)]
	private CarsModificationEventUid $event;
	
	/** ID модели */
    #[Assert\Uuid]
    #[Assert\NotBlank]
	#[ORM\Column(type: CarsModelUid::TYPE)]
	private CarsModelUid $model;
	
	
	public function __construct(CarsModificationUid $id = null)
	{
		$this->id =  $id ?: new CarsModificationUid();
	}

    public function __toString(): string
    {
        return (string) $this->id;
    }

	public function getId() : CarsModificationUid
	{
		return $this->id;
	}
	
	public function getEvent() : CarsModificationEventUid
	{
		return $this->event;
	}
	
	public function setEvent(CarsModificationEventUid|CarsModificationEvent $event) : void
	{
		$this->event = $event instanceof CarsModificationEvent ? $event->getId() : $event;
	}
	
	public function setModel(CarsModelUid|CarsModel $model) : void
	{
		$this->model = $model instanceof CarsModel ? $model->getId() : $model;
	}
}