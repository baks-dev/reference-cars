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

namespace BaksDev\Reference\Cars\Repository\Models\AllCarsModel;


use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEvent;
use BaksDev\Reference\Cars\Entity\Model\Image\CarsModelImage;
use BaksDev\Reference\Cars\Entity\Model\Trans\CarsModelTrans;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;

final class AllCarsModel implements AllCarsModelInterface
{
    private ?SearchDTO $search = null;

    private ?CarsBrandUid $brand = null;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
    ) {}

    public function search(SearchDTO $search): self
    {
        $this->search = $search;
        return $this;
    }

    public function brand(CarsBrandUid $brand): self
    {
        $this->brand = $brand;
        return $this;
    }


    /** Метод возвращает пагинатор CarsModel */
    public function fetchAllCarsModelAssociative(): PaginatorInterface
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();


        $dbal
            ->addSelect('main.id')
            ->addSelect('main.event')
            ->from(CarsModel::class, 'main');

        $dbal
            ->addSelect('event.class')
            ->addSelect('event.code')
            ->addSelect('event.year_from')
            ->addSelect('event.year_to')
            ->leftJoin(
                'main',
                CarsModelEvent::class,
                'event',
                'event.id = main.event'
            );

        $dbal
            ->addSelect('trans.name AS model_name')
            ->addSelect('trans.description AS model_desc')
            ->leftJoin(
                'main',
                CarsModelTrans::class,
                'trans',
                'trans.event = main.event AND trans.local = :local'
            );

        $dbal
            ->addSelect("CONCAT('/upload/".$dbal->table(CarsModelImage::class)."' , '/', image.name) AS image_name")
            ->addSelect('image.ext AS image_ext')
            ->addSelect('image.cdn AS image_cdn')
            ->leftJoin(
                'main',
                CarsModelImage::class,
                'image',
                'image.event = main.event'
            );


        $dbal
            ->addSelect('brand.id AS brand_id')
            ->leftJoin(
                'main',
                CarsBrand::class,
                'brand',
                'brand.id = main.brand'
            );

        $dbal
            ->addSelect('brand_trans.name AS brand_name')
            ->leftJoin(
                'brand',
                CarsBrandTrans::class,
                'brand_trans',
                'brand_trans.event = brand.event AND brand_trans.local = :local'
            );


        if($this->brand)
        {
            $dbal->andWhere('main.brand = :brand')
                ->setParameter('brand', $this->brand, CarsBrandUid::TYPE);
        }

        /* Поиск */
        if($this->search?->getQuery())
        {
            $dbal
                ->createSearchQueryBuilder($this->search)
                ->addSearchLike('trans.name')
                ->addSearchLike('event.code');
        }

        return $this->paginator->fetchAllAssociative($dbal);
    }
}
