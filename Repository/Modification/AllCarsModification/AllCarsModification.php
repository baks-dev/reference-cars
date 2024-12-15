<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Cars\Repository\Modification\AllCarsModification;


use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEvent;
use BaksDev\Reference\Cars\Entity\Model\Trans\CarsModelTrans;
use BaksDev\Reference\Cars\Entity\Modification\CarsModification;
use BaksDev\Reference\Cars\Entity\Modification\Event\CarsModificationEvent;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;

final class AllCarsModification implements AllCarsModificationInterface
{
    private ?SearchDTO $search = null;

    private ?CarsModelUid $model = null;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
    )
    {

    }

    public function search(SearchDTO $search): self
    {
        $this->search = $search;
        return $this;
    }


    public function model(CarsModelUid $model): self
    {
        $this->model = $model;
        return $this;
    }

    /** Метод возвращает пагинатор CarsModification */
    public function fetchAllCarsModificationAssociative(): PaginatorInterface
    {
        $qb = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        //$qb->select('*');

        $qb
            ->addSelect('main.id')
            ->addSelect('main.event')
            ->from(CarsModification::class, 'main');

        $qb
            ->addSelect('event.modification')
            ->leftJoin(
                'main',
                CarsModificationEvent::class,
                'event',
                'event.id = main.event'
            );


        $qb
            ->leftJoin(
                'main',
                CarsModel::class,
                'model',
                'model.id = main.model'
            );

        $qb
            ->addSelect('model_event.code')
            ->leftJoin(
                'model',
                CarsModelEvent::class,
                'model_event',
                'model_event.id = model.event'
            );

        $qb
            ->addSelect('model_trans.name AS model_name')
            ->leftJoin(
                'model',
                CarsModelTrans::class,
                'model_trans',
                'model_trans.event = model.event AND model_trans.local = :local'
            );


        $qb
            ->leftJoin(
                'model',
                CarsBrand::class,
                'brand',
                'brand.id = model.brand'
            );

        $qb
            ->addSelect('brand_trans.name AS brand_name')
            ->leftJoin(
                'brand',
                CarsBrandTrans::class,
                'brand_trans',
                'brand_trans.event = brand.event AND brand_trans.local = :local'
            );

        if($this->model)
        {
            $qb->andWhere('main.model = :model')
                ->setParameter('model', $this->model, CarsModelUid::TYPE);
        }


        /* Поиск */
        if($this->search?->getQuery())
        {
            $qb
                ->createSearchQueryBuilder($this->search)
                ->addSearchLike('event.modification')
                ->addSearchLike('model_event.code')
                ->addSearchLike('brand_trans.name')
                ->addSearchLike('model_trans.name')//->addSearchLike('personal.location')
            ;
        }

        return $this->paginator->fetchAllAssociative($qb);
    }
}
