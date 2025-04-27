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

namespace BaksDev\Reference\Cars\Entity\Modification\Characteristics\Disc;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use BaksDev\Reference\Cars\Type\Modification\Disc\CarsModificationDiscUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* Перевод CarsModificationDisc */

#[ORM\Entity]
#[ORM\Table(name: 'cars_modification_disc')]
class CarsModificationDisc extends EntityEvent
{
    /** Идентификатор */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Id]
    #[ORM\Column(type: CarsModificationDiscUid::TYPE)]
    private CarsModificationDiscUid $id;

    /** Связь на характеристику */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\ManyToOne(targetEntity: CarsModificationCharacteristics::class, inversedBy: "disc")]
    #[ORM\JoinColumn(name: 'characteristic', referencedColumnName: "id")]
    private CarsModificationCharacteristics $characteristic;

    /** Вылет (ET) */
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $et = null;

    /* Размер 9.5x18 */

    /** Диаметр 18″ */
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $diameter = null;

    /** Ширина 9.5″ */
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $width = null;


    public function __construct(CarsModificationCharacteristics $characteristic)
    {
        $this->characteristic = $characteristic;
        $this->id = new CarsModificationDiscUid();
    }

    public function __toString(): string
    {
        return (string) $this->characteristic;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CarsModificationDiscInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof CarsModificationDiscInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

}