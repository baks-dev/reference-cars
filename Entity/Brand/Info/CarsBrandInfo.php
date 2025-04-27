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

namespace BaksDev\Reference\Cars\Entity\Brand\Info;

use BaksDev\Core\Entity\EntityReadonly;
use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* Неизменяемые данные Продукта */

#[ORM\Entity]
#[ORM\Table(name: 'cars_brand_info')]
class CarsBrandInfo extends EntityReadonly
{
    /** Идентификатор бренда */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: CarsBrandUid::TYPE)]
    private CarsBrandUid $brand;

    /** Событие */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\OneToOne(targetEntity: CarsBrandEvent::class, inversedBy: 'info')]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private CarsBrandEvent $event;

    /** Семантическая ссылка на бренд */
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[\w\-]+$/iu')]
    #[ORM\Column(type: Types::STRING, unique: true)]
    private string $url;

    /** Просмотры */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $review = 0;

    public function __construct(CarsBrandEvent $event)
    {
        $this->event = $event;
        $this->brand = $event->getMain();
    }

    public function __toString(): string
    {
        return (string) $this->brand;
    }

    public function getBrand(): CarsBrandUid
    {
        return $this->brand;
    }

    public function setEvent(CarsBrandEvent $event): self
    {
        $this->event = $event;
        return $this;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CarsBrandInfoInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {
        if($dto instanceof CarsBrandInfoInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    //    public function updateUrlUniq() : void
    //    {
    //        $this->url = uniqid($this->url.'_', false);
    //    }
}
