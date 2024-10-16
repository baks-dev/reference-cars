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

declare(strict_types=1);

namespace BaksDev\Reference\Cars\Repository\Models\EventCarModelByName;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Reference\Cars\Command\Upgrade\CarsModelInterface;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEvent;
use BaksDev\Reference\Cars\Entity\Model\Trans\CarsModelTrans;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EventCarModelByName implements EventCarModelByNameInterface
{
    private EntityManagerInterface $entityManager;
    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    )
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * Метод получает активное событие модели автомобиля по названию
     */
    public function getEvent(CarsModelInterface $model): ?CarsModelEvent
    {
        $qb = $this->entityManager->createQueryBuilder();

        $locale = new Locale($this->translator->getLocale());

        $qb->select('event');
        $qb->from(CarsModelTrans::class, 'trans');

        $qb->join(
            CarsModelEvent::class,
            'event',
            'WITH',
            'event.id = trans.event'
        );


        $qb->join(
            CarsBrandTrans::class,
            'brand_trans',
            'WITH',
            'brand_trans.name = :brand AND brand_trans.local = :local'
        );


        $qb->join(
            CarsBrandEvent::class,
            'brand_event',
            'WITH',
            'brand_event.id = brand_trans.event'
        );

        $qb->join(
            CarsBrand::class,
            'brand',
            'WITH',
            'brand.event = brand_event.id'
        );


        $qb->join(
            CarsModel::class,
            'model',
            'WITH',
            'model.event = event.id AND model.brand = brand.id'
        );

        $qb->where('trans.name = :model');
        $qb->andWhere('trans.local = :local');

        $qb->setParameter('model', $model::getModel());
        $qb->setParameter('brand', $model::getBrand());
        $qb->setParameter('local', $locale, Locale::TYPE);

        return $qb->getQuery()->getOneOrNullResult();
    }
}