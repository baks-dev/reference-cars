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
    #[ORM\OneToOne(targetEntity: Motor\CarsModificationMotor::class, mappedBy: 'characteristic', cascade: ['all'], fetch: 'EAGER')]
    private Motor\CarsModificationMotor $motor;

    /** Шаси */
    #[Assert\Valid]
    #[ORM\OneToOne(targetEntity: Chassis\CarsModificationChassis::class, mappedBy: 'characteristic', cascade: ['all'], fetch: 'EAGER')]
    private Chassis\CarsModificationChassis $chassis;

    /* Подбор комплектующих */

    /** Шины */
    #[Assert\Valid]
    #[ORM\OneToMany(targetEntity: Tires\CarsModificationTires::class, mappedBy: 'characteristic', cascade: ['all'], fetch: 'EAGER')]
    private Collection $tire;

    /** Диски */
    #[Assert\Valid]
    #[ORM\OneToMany(targetEntity: Disc\CarsModificationDisc::class, mappedBy: 'characteristic', cascade: ['all'], fetch: 'EAGER')]
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