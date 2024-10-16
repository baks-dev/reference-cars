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

namespace BaksDev\Reference\Cars\Entity\Model\Info;

use BaksDev\Core\Entity\EntityReadonly;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEvent;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* Неизменяемые данные Продукта */

#[ORM\Entity]
#[ORM\Table(name: 'cars_model_info')]
#[ORM\Index(columns: ['url'])]
class CarsModelInfo extends EntityReadonly
{
    public const TABLE = 'cars_model_info';

    /** ID модели */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: CarsModelUid::TYPE)]
    private CarsModelUid $model;

    /** ID события */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\OneToOne(inversedBy: 'info', targetEntity: CarsModelEvent::class)]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private CarsModelEvent $event;

    /** Семантическая ссылка на бренд */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING)]
    private string $url;

    /** Просмотры */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $review = 0;

    public function __construct(CarsModelEvent $event)
    {
        $this->event = $event;
        $this->model = $event->getMain();
    }

    public function __toString(): string
    {
        return (string) $this->model;
    }

    public function getModel(): CarsModelUid
    {
        return $this->model;
    }

    public function setEvent(CarsModelEvent $event): self
    {
        $this->event = $event;
        return $this;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CarsModelInfoInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {
        if($dto instanceof CarsModelInfoInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function updateUrlUniq(): void
    {
        $this->url = uniqid($this->url.'_', false);
    }
}
