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

namespace BaksDev\Reference\Cars\Entity\Modification;

use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Modification\Event\CarsModificationEvent;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use BaksDev\Reference\Cars\Type\Modification\Event\CarsModificationEventUid;
use BaksDev\Reference\Cars\Type\Modification\Id\CarsModificationUid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/* CarModification */

#[ORM\Entity]
#[ORM\Table(name: 'cars_modification')]
#[ORM\Index(columns: ['model'])]
class CarsModification
{
    public const TABLE = 'cars_modification';

    /** ID */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Id]
    #[ORM\Column(type: CarsModificationUid::TYPE)]
    private CarsModificationUid $id;

    /** ID События */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Column(type: CarsModificationEventUid::TYPE, unique: true)]
    private CarsModificationEventUid $event;

    /** ID модели */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Column(type: CarsModelUid::TYPE)]
    private CarsModelUid $model;


    public function __construct(?CarsModificationUid $id = null)
    {
        $this->id = $id ?: new CarsModificationUid();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getEvent(): CarsModificationEventUid
    {
        return $this->event;
    }

    public function setEvent(CarsModificationEventUid|CarsModificationEvent $event): void
    {
        $this->event = $event instanceof CarsModificationEvent ? $event->getId() : $event;
    }

    public function getId(): CarsModificationUid
    {
        return $this->id;
    }

    public function setModel(CarsModelUid|CarsModel $model): void
    {
        $this->model = $model instanceof CarsModel ? $model->getId() : $model;
    }
}