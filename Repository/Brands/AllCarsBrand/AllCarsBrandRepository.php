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

namespace BaksDev\Reference\Cars\Repository\Brands\AllCarsBrand;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\Paginator;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use BaksDev\Reference\Cars\Entity\Brand\Logo\CarsBrandLogo;
use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;

final class AllCarsBrandRepository implements AllCarsBrandInterface
{
    private DBALQueryBuilder $DBALQueryBuilder;

    private ?SearchDTO $search = null;

    private Paginator $paginator;

    public function __construct(
        Paginator $paginator,
        DBALQueryBuilder $DBALQueryBuilder,
    )
    {
        $this->DBALQueryBuilder = $DBALQueryBuilder;
        $this->paginator = $paginator;
    }

    public function search(SearchDTO $search): self
    {
        $this->search = $search;
        return $this;
    }


    public function fetchAllAllCarsBrandAssociative(): Paginator
    {
        $qb = $this->DBALQueryBuilder->createQueryBuilder(self::class)->bindLocal();

        //$qb->select('*');

        $qb
            ->addSelect('main.id')
            ->addSelect('main.event')
            ->from(CarsBrand::TABLE, 'main');

        $qb
            ->leftJoin(
                'main',
                CarsBrandEvent::TABLE,
                'event',
                'event.id = main.event'
            );

        $qb
            ->addSelect('trans.name AS brand_name')
            ->addSelect('trans.description AS brand_desc')
            ->leftJoin(
                'main',
                CarsBrandTrans::TABLE,
                'trans',
                'trans.event = main.event AND trans.local = :local'
            );

        $qb
            ->addSelect("CONCAT('/upload/".CarsBrandLogo::TABLE."' , '/', logo.name) AS logo_name")
            ->addSelect('logo.ext AS logo_ext')
            ->addSelect('logo.cdn AS logo_cdn')
            ->leftJoin(
                'main',
                CarsBrandLogo::TABLE,
                'logo',
                'logo.event = main.event'
            );

        /* Поиск */
        if($this->search?->getQuery())
        {
            $qb
                ->createSearchQueryBuilder($this->search)
                ->addSearchLike('trans.name')
                //->addSearchLike('personal.location')
            ;
        }


        $qb->addOrderBy('trans.name');




        return $this->paginator->fetchAllAssociative($qb);

        //        return $qb
        //            // ->enableCache('reference-cars', 3600)
        //            ->fetchAllAssociative();
    }
}