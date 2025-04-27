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

namespace BaksDev\Reference\Cars\Entity\Brand\Event;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Core\Type\Modify\Modify\ModifyActionNew;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Info\CarsBrandInfo;
use BaksDev\Reference\Cars\Entity\Brand\Logo\CarsBrandLogo;
use BaksDev\Reference\Cars\Entity\Brand\Modify\CarsBrandModify;
use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;
use BaksDev\Reference\Cars\Type\Brand\Event\CarsBrandEventUid;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'cars_brand_event')]
class CarsBrandEvent extends EntityEvent
{
    /** Идентификатор события */
    #[ORM\Id]
    #[ORM\Column(type: CarsBrandEventUid::TYPE)]
    private CarsBrandEventUid $id;

    /** Идентификатор бренда */
    #[ORM\Column(type: CarsBrandUid::TYPE, nullable: false)]
    private ?CarsBrandUid $main = null;

    /**
     * Лого
     */
    #[ORM\OneToOne(targetEntity: CarsBrandLogo::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private ?CarsBrandLogo $logo = null;

    /**
     * Модификатор
     */
    #[ORM\OneToOne(targetEntity: CarsBrandModify::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private CarsBrandModify $modify;

    /**
     * Перевод
     */
    #[ORM\OneToMany(targetEntity: CarsBrandTrans::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private Collection $translate;


    /**
     * Информация о бренде
     */
    #[ORM\OneToOne(targetEntity: CarsBrandInfo::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private ?CarsBrandInfo $info = null;


    public function __construct()
    {
        $this->id = new CarsBrandEventUid();
        $this->modify = new CarsBrandModify($this, new ModifyAction(ModifyActionNew::class));

    }

    public function __clone()
    {
        $this->id = clone $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getMain(): ?CarsBrandUid
    {
        return $this->main;
    }

    public function setMain(CarsBrandUid|CarsBrand $brand): void
    {
        $this->main = $brand instanceof CarsBrand ? $brand->getId() : $brand;
    }

    public function getId(): CarsBrandEventUid
    {
        return $this->id;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CarsBrandEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof CarsBrandEventInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function getUploadLogo(): CarsBrandLogo
    {
        return $this->logo ?: $this->logo = new CarsBrandLogo($this);
    }

    public function getNameByLocale(Locale $locale): ?string
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
