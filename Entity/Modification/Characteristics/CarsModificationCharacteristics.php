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

namespace BaksDev\Reference\Cars\Entity\Modification\Characteristics;


use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Reference\Cars\Entity\Modification\Event\CarsModificationEvent;
use BaksDev\Reference\Cars\Type\Modification\Characteris\CarsModificationCharacteristicsUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* CarsModificationCharacteristics */

#[ORM\Entity]
#[ORM\Table(name: 'cars_modification_characteristics')]
#[ORM\Index(columns: ['model'])]
class CarsModificationCharacteristics extends EntityEvent
{
    public const TABLE = 'cars_modification_characteristics';

    /** Идентификатор */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Id]
    #[ORM\Column(type: CarsModificationCharacteristicsUid::TYPE)]
    private CarsModificationCharacteristicsUid $id;

    /** ID события */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\ManyToOne(targetEntity: CarsModificationEvent::class, inversedBy: "characteristic")]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: "id")]
    private CarsModificationEvent $event;

//    /** Семантическая ссылка */
//    #[Assert\NotBlank]
//    #[ORM\Column(type: Types::STRING, nullable: true)]
//    private ?string $url = null;

    /** Модель двигателя */
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private string $model;

    /* Даты выпуска модификации с таким двигателем */

    /** Год начала выпуска модификации  */
    #[Assert\NotBlank]
    #[ORM\Column(name: 'year_from', type: Types::SMALLINT)]
    private int $from;

    /** Год окончания выпуска модификации (null - по наст. время) */
    #[ORM\Column(name: 'year_to', type: Types::SMALLINT, nullable: true)]
    private ?int $to = null;


    /** Двигатель */
    #[Assert\Valid]
    #[ORM\OneToOne(targetEntity: Motor\CarsModificationMotor::class, mappedBy: 'characteristic', cascade: ['all'])]
    private Motor\CarsModificationMotor $motor;

    /** Шаси */
    #[Assert\Valid]
    #[ORM\OneToOne(targetEntity: Chassis\CarsModificationChassis::class, mappedBy: 'characteristic', cascade: ['all'])]
    private Chassis\CarsModificationChassis $chassis;

    /* Подбор комплектующих */

    /** Шины */
    #[Assert\Valid]
    #[ORM\OneToMany(targetEntity: Tires\CarsModificationTires::class, mappedBy: 'characteristic', cascade: ['all'])]
    private Collection $tire;

    /** Диски */
    #[Assert\Valid]
    #[ORM\OneToMany(targetEntity: Disc\CarsModificationDisc::class, mappedBy: 'characteristic', cascade: ['all'])]
    private Collection $disc;


    public function __construct(CarsModificationEvent $event)
    {
        $this->event = $event;
        $this->id = new CarsModificationCharacteristicsUid();
    }

    public function __clone(): void
    {
        $this->id = clone $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    /**
     * Event
     */
    public function getEvent(): CarsModificationEvent
    {
        return $this->event;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CarsModificationCharacteristicsInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof CarsModificationCharacteristicsInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

}