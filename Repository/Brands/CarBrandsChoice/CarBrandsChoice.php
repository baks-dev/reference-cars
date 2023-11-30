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

namespace BaksDev\Reference\Cars\Repository\Brands\CarBrandsChoice;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use BaksDev\Reference\Cars\Entity\Brand\Info\CarsBrandInfo;
use BaksDev\Reference\Cars\Entity\Brand\Logo\CarsBrandLogo;
use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Modification\CarsModification;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\Tires\CarsModificationTires;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use Generator;

final class CarBrandsChoice implements CarBrandsChoiceInterface
{
    private ORMQueryBuilder $ORMQueryBuilder;
    private DBALQueryBuilder $DBALQueryBuilder;

    public function __construct(
        ORMQueryBuilder $ORMQueryBuilder,
        DBALQueryBuilder $DBALQueryBuilder
    )
    {
        $this->ORMQueryBuilder = $ORMQueryBuilder;
        $this->DBALQueryBuilder = $DBALQueryBuilder;
    }

    public function getCollectionByTires(): Generator
    {
        $qb = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $qb->from(CarsBrand::TABLE, 'main');

        $qb->leftJoin(
            'main',
            CarsBrandInfo::TABLE,
            'info',
            'info.brand = main.id'
        );

        $qb->leftJoin(
            'main',
            CarsBrandTrans::TABLE,
            'trans',
            'trans.event = main.event AND trans.local = :local'
        );


        $objQueryExistModel = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $objQueryExistModel
            ->select('1')
            ->from(CarsModel::TABLE, 'model')
            ->where('model.brand = main.id');


        $objQueryExistModel->join(
            'model',
            CarsModification::TABLE,
            'mod',
            'mod.model = model.id');

        $objQueryExistModel->join(
            'mod',
            CarsModificationCharacteristics::TABLE,
            'char',
            'char.event = mod.event');

        $objQueryExistModel->join(
            'char',
            CarsModificationTires::TABLE,
            'tires',
            'tires.characteristic = char.id');


        //$qb->andWhere($qb->expr()->exists($objQueryExistModel->getDQL()));
        $qb->andWhere('EXISTS('.$objQueryExistModel->getSQL().')');

        $qb->orderBy('trans.name');


        $qb->addSelect('main.id AS value');
        $qb->addSelect('trans.name AS attr');
        $qb->addSelect('info.review AS option');
        
        return $qb
            ->enableCache('reference-cars', 86400)
            ->fetchAllHydrate(CarsBrandUid::class);
    }


    public function getDetailCollectionByTires(): ?array
    {
        $qb = $this->ORMQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();


        //        $select = sprintf('new %s(main.id, trans.name, info.review)', CarsBrandUid::class);
        //        $qb->addSelect($select);

        $qb->from(CarsBrand::class, 'main');

        $qb
            ->addSelect('info.review AS brand_review')
            ->addSelect('info.url AS brand_url')
            ->leftJoin(
                CarsBrandInfo::class,
                'info',
                'WITH',
                'info.brand = main.id'
            );

        $qb
            ->addSelect('trans.name AS brand_name')
            ->leftJoin(
                CarsBrandTrans::class,
                'trans',
                'WITH',
                'trans.event = main.event AND trans.local = :local'
            );


        $qb
            //->addSelect('logo.name AS image_name')
            ->addSelect('logo.ext AS image_ext')
            ->addSelect('logo.cdn AS image_cdn')
            ->leftJoin(
                CarsBrandLogo::class,
                'logo',
                'WITH',
                'logo.event = main.event'
            );

        $qb->addSelect("
        			CASE
        			   WHEN logo.name IS NOT NULL THEN
        					CONCAT ( '/upload/".CarsBrandLogo::TABLE."' , '/', logo.name)
        			   ELSE logo.name
        			END AS image_name
        		"
        );


        //        $qb->addSelect(
        //            "CASE
        //			   WHEN logo.name IS NOT NULL THEN
        //					CONCAT ( '/upload/".CarsBrandLogo::TABLE."' , '/', logo.name)
        //			   ELSE NULL
        //			END AS image_name
        //		"
        //        );


        $objQueryExistModel = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $objQueryExistModel
            ->select('1')
            ->from(CarsModel::class, 'model')
            ->where('model.brand = main.id');


        $objQueryExistModel->join(
            CarsModification::class,
            'mod', 'WITH',
            'mod.model = model.id');

        $objQueryExistModel->join(
            CarsModificationCharacteristics::class,
            'char', 'WITH',
            'char.event = mod.event');

        $objQueryExistModel->join(
            CarsModificationTires::class,
            'tires', 'WITH',
            'tires.characteristic = char.id');


        $qb->andWhere($qb->expr()->exists($objQueryExistModel->getDQL()));

        $qb->orderBy('trans.name');

        return $qb
            ->enableCache('reference-cars', 86400)
            ->getResult();
    }


    public function getCollection(): Generator
    {
        $qb = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $qb->from(CarsBrand::TABLE, 'main');

        $qb->leftJoin(
            'main',
            CarsBrandTrans::TABLE,
            'trans',
            'trans.event = main.event AND trans.local = :local'
        );

        $qb->orderBy('trans.name');


        $qb->addSelect('main.id AS value');
        $qb->addSelect('trans.name AS attr');

        return $qb
            ->enableCache('reference-cars', 86400)
            ->fetchAllHydrate(CarsBrandUid::class);
    }
}