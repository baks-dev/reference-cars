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

namespace BaksDev\Reference\Cars\Entity\Modification\Event;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Reference\Cars\Entity\Modification\CarsModification;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use BaksDev\Reference\Cars\Entity\Modification\Info\CarsModificationInfo;
use BaksDev\Reference\Cars\Entity\Modification\Modify\CarsModificationModify;
use BaksDev\Reference\Cars\Type\Modification\Event\CarsModificationEventUid;
use BaksDev\Reference\Cars\Type\Modification\Id\CarsModificationUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;


/* CarModificationEvent */

#[ORM\Entity]
#[ORM\Table(name: 'cars_modification_event')]
#[ORM\Index(columns: ['modification'])]
class CarsModificationEvent extends EntityEvent
{
    /** ID */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Id]
    #[ORM\Column(type: CarsModificationEventUid::TYPE)]
    private CarsModificationEventUid $id;

    /** ID CarModification */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Column(type: CarsModificationUid::TYPE)]
    private ?CarsModificationUid $main = null;

    /** Модификация */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING)]
    private string $modification;

    /** Характеристика модификации модели */
    #[Assert\Valid]
    #[ORM\OneToMany(targetEntity: CarsModificationCharacteristics::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private Collection $characteristic;

    /** Модификатор события */
    #[Assert\Valid]
    #[ORM\OneToOne(targetEntity: CarsModificationModify::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private CarsModificationModify $modify;


    /** Информация о модификации */
    #[ORM\OneToOne(targetEntity: CarsModificationInfo::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private ?CarsModificationInfo $info = null;


    public function __construct()
    {
        $this->id = new CarsModificationEventUid();
        $this->modify = new CarsModificationModify($this);

    }

    public function __clone()
    {
        $this->id = clone $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getMain(): ?CarsModificationUid
    {
        return $this->main;
    }

    public function setMain(CarsModificationUid|CarsModification $main): void
    {
        $this->main = $main instanceof CarsModification ? $main->getId() : $main;
    }

    public function getId(): CarsModificationEventUid
    {
        return $this->id;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CarModificationEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {
        if($dto instanceof CarModificationEventInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

}