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

namespace BaksDev\Reference\Cars\Repository\Brands\ActiveCarsBrand;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;

final class ActiveEventCarsBrandRepository implements ActiveEventCarsBrandInterface
{
    private ORMQueryBuilder $ORMQueryBuilder;

    public function __construct(ORMQueryBuilder $ORMQueryBuilder)
    {
        $this->ORMQueryBuilder = $ORMQueryBuilder;
    }

    /**
     * Метод возвращает активное событие по идентификатору
     */
    public function getCurrentEvent(CarsBrandUid $id) : ?CarsBrandEvent
    {
        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $qb
            ->from(CarsBrand::class, 'main')
            ->where('main.id = :id')
            ->setParameter('id', $id, CarsBrandUid::TYPE)
        ;

        $qb
            ->select('event')
            ->leftJoin(CarsBrandEvent::class,
                'event',
                'WITH',
                'event.id = main.event'
            );

        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }


    /**
     * Метод возвращает все активные события
     */
    public function getAllCurrentEvents() : ?array
    {
        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $qb->from(CarsBrand::class, 'main');

        $qb
            ->select('event')
            ->leftJoin(CarsBrandEvent::class,
                'event',
                'WITH',
                'event.id = main.event'
            );

        return $qb->getQuery()->getResult();
    }

    /**
     * Метод возвращает активное событие по названию
     */
    public function getEventByName(string $name) : ?CarsBrandEvent
    {
        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class);


        $qb
            ->from(CarsBrandTrans::class, 'trans')
            ->where('trans.name = :name')
            ->setParameter('name', $name)
        ;

        $qb->join(
            CarsBrand::class,
            'main',
            'WITH',
            'main.event = trans.event'
        );

        $qb
            ->select('event')
            ->leftJoin(CarsBrandEvent::class,
            'event',
                'WITH',
                'event.id = trans.event'
        );

        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}