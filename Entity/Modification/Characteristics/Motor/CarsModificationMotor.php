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

namespace BaksDev\Reference\Cars\Entity\Modification\Characteristics\Motor;


use App\Module\Users\User\Entity\User;
use App\Module\Users\User\Type\Id\UserUid;
use App\System\Type\Ip\IpAddress;
use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* CarsModificationMotor */

#[ORM\Entity]
#[ORM\Table(name: 'cars_modification_motor')]
#[ORM\Index(columns: ['engine', 'power'])]
class CarsModificationMotor extends EntityEvent
{
    public const TABLE = 'cars_modification_motor';

    /** ID характеристики */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: CarsModificationCharacteristics::class, inversedBy: 'motor')]
    #[ORM\JoinColumn(name: 'characteristic', referencedColumnName: 'id')]
    private CarsModificationCharacteristics $characteristic;

    /** Тип двигателя (бензин/дизель ...) */
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $fuel = null;

    /** Объем двигателя (л) */
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $engine = null;

    /** Мощность двигателя, л.с. */
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $power = null;

    /** Привод */
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $drive = null;

    public function __construct(CarsModificationCharacteristics $characteristic)
    {
        $this->characteristic = $characteristic;
    }

    public function __toString(): string
    {
        return (string) $this->characteristic;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CarsModificationMotorInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof CarsModificationMotorInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

}