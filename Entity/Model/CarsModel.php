<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
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

    public function __construct(?CarsModelUid $id = null)
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