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
    public const TABLE = 'cars_model_event';

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
    #[ORM\OneToOne(targetEntity: CarsModelImage::class, mappedBy: 'event', cascade: ['all'])]
    private ?CarsModelImage $image = null;

    /** Модификатор */
    #[ORM\OneToOne(targetEntity: CarsModelModify::class, mappedBy: 'event', cascade: ['all'])]
    private CarsModelModify $modify;

    /** Перевод */
    #[ORM\OneToMany(targetEntity: CarsModelTrans::class, mappedBy: 'event', cascade: ['all'])]
    private Collection $translate;

    /** Информация о модели */
    #[ORM\OneToOne(targetEntity: CarsModelInfo::class, mappedBy: 'event', cascade: ['all'])]
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