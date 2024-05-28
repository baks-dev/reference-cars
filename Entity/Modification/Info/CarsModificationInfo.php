<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Cars\Entity\Modification\Info;

use BaksDev\Core\Entity\EntityReadonly;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEvent;
use BaksDev\Reference\Cars\Entity\Modification\Event\CarsModificationEvent;
use BaksDev\Reference\Cars\Type\Modification\Id\CarsModificationUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* Неизменяемые данные Продукта */

#[ORM\Entity]
#[ORM\Table(name: 'cars_modification_info')]
#[ORM\Index(columns: ['url'])]
class CarsModificationInfo extends EntityReadonly
{
    public const TABLE = 'cars_modification_info';

    /** ID модификации */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: CarsModificationUid::TYPE)]
    private CarsModificationUid $modification;

    /** ID события */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\OneToOne(inversedBy: 'info', targetEntity: CarsModificationEvent::class)]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private CarsModificationEvent $event;

    /** Семантическая ссылка на модификацию */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING)]
    private string $url;

    /** Просмотры */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $review = 0;

    public function __construct(CarsModificationEvent $event)
    {
        $this->event = $event;
        $this->modification = $event->getMain();
    }

    public function __toString(): string
    {
        return (string) $this->modification;
    }

    public function getModification(): CarsModificationUid
    {
        return $this->modification;
    }

    public function setEvent(CarsModificationEvent $event): self
    {
        $this->event = $event;
        return $this;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if ($dto instanceof CarsModificationInfoInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {
        if ($dto instanceof CarsModificationInfoInterface || $dto instanceof self) {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function updateUrlUniq() : void
    {
        $this->url = uniqid($this->url.'_', false);
    }
}
