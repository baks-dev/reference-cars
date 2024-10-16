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

namespace BaksDev\Reference\Cars\Entity\Model;

use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEvent;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use BaksDev\Reference\Cars\Type\Model\Event\CarsModelEventUid;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/* CarsModel */

#[ORM\Entity]
#[ORM\Table(name: 'cars_model')]
#[ORM\Index(columns: ['brand'])]
class CarsModel
{
    public const TABLE = 'cars_model';

    /** ID */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Id]
    #[ORM\Column(type: CarsModelUid::TYPE)]
    private CarsModelUid $id;

    /** ID События */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Column(type: CarsModelEventUid::TYPE, unique: true)]
    private CarsModelEventUid $event;

    /** ID Бренда */
    #[Assert\Uuid]
    #[ORM\Column(type: CarsBrandUid::TYPE, nullable: true)]
    private ?CarsBrandUid $brand = null;

    public function __construct(CarsModelUid $id = null)
    {
        $this->id = $id ?: new CarsModelUid();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getEvent(): CarsModelEventUid
    {
        return $this->event;
    }

    public function setEvent(CarsModelEventUid|CarsModelEvent $event): void
    {
        $this->event = $event instanceof CarsModelEvent ? $event->getId() : $event;
    }

    public function getId(): CarsModelUid
    {
        return $this->id;
    }

    public function getBrand(): ?CarsBrandUid
    {
        return $this->brand;
    }

    /**
     * Brand
     */
    public function setBrand(CarsBrandUid|CarsBrand $brand): void
    {
        $this->brand = $brand instanceof CarsBrand ? $brand->getId() : $brand;
    }
}