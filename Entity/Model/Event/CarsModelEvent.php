<?php
/*
 *  Copyright 2023-2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Cars\Entity\Model\Event;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Model\Image\CarsModelImage;
use BaksDev\Reference\Cars\Entity\Model\Info\CarsModelInfo;
use BaksDev\Reference\Cars\Entity\Model\Modify\CarsModelModify;
use BaksDev\Reference\Cars\Entity\Model\Trans\CarsModelTrans;
use BaksDev\Reference\Cars\Type\Model\Event\CarsModelEventUid;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use BaksDev\Reference\Cars\Type\Model\Type\CarsModelClass;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;


/* CarsModelEvent */

#[ORM\Entity]
#[ORM\Table(name: 'cars_model_event')]
class CarsModelEvent extends EntityEvent
{
    /** ID */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Id]
    #[ORM\Column(type: CarsModelEventUid::TYPE)]
    private CarsModelEventUid $id;

    /** ID CarsModel */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Column(type: CarsModelUid::TYPE)]
    private ?CarsModelUid $main = null;

    /** Класс автомобиля  */
    #[Assert\NotBlank]
    #[ORM\Column(type: CarsModelClass::TYPE)]
    private CarsModelClass $class;

    /** Код автомобиля  */
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $code = null;

    /** Год начала выпуска авто  */
    #[Assert\NotBlank]
    #[ORM\Column(name: 'year_from', type: Types::SMALLINT)]
    private int $from;

    /** Год окончания выпуска авто (null - по наст. время) */
    #[ORM\Column(name: 'year_to', type: Types::SMALLINT, nullable: true)]
    private ?int $to = null;

    /** Обложка модели */
    #[ORM\OneToOne(targetEntity: CarsModelImage::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private ?CarsModelImage $image = null;

    /** Модификатор */
    #[ORM\OneToOne(targetEntity: CarsModelModify::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private CarsModelModify $modify;

    /** Перевод */
    #[ORM\OneToMany(targetEntity: CarsModelTrans::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private Collection $translate;

    /** Информация о модели */
    #[ORM\OneToOne(targetEntity: CarsModelInfo::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private ?CarsModelInfo $info = null;


    public function __construct()
    {
        $this->id = new CarsModelEventUid();
        $this->modify = new CarsModelModify($this);
    }

    public function __clone()
    {
        $this->id = clone $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getMain(): ?CarsModelUid
    {
        return $this->main;
    }

    public function setMain(CarsModelUid|CarsModel $main): void
    {
        $this->main = $main instanceof CarsModel ? $main->getId() : $main;
    }

    public function getId(): CarsModelEventUid
    {
        return $this->id;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CarsModelEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof CarsModelEventInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function getUploadImage(): CarsModelImage
    {
        return $this->image ?: $this->image = new CarsModelImage($this);
    }

    public function getNameByLocale(Locale $locale): ?string
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

    public function isImage(): bool
    {
        return $this->image !== null;
    }
}