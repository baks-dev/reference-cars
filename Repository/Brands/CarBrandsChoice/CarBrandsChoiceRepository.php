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
use BaksDev\Reference\Cars\Entity\Brand\Modify\CarsBrandModify;
use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Modification\CarsModification;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\Tires\CarsModificationTires;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use Generator;

final class CarBrandsChoiceRepository implements CarBrandsChoiceInterface
{
    //private ORMQueryBuilder $ORMQueryBuilder;
    private DBALQueryBuilder $DBALQueryBuilder;

    public function __construct(
        //ORMQueryBuilder $ORMQueryBuilder,
        DBALQueryBuilder $DBALQueryBuilder
    )
    {
        //$this->ORMQueryBuilder = $ORMQueryBuilder;
        $this->DBALQueryBuilder = $DBALQueryBuilder;
    }

    public function getCollectionByTires(): Generator
    {

        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal->from(CarsBrand::class, 'main');

        $dbal->leftJoin(
            'main',
            CarsBrandInfo::class,
            'info',
            'info.brand = main.id'
        );

        $dbal->leftJoin(
            'main',
            CarsBrandTrans::class,
            'trans',
            'trans.event = main.event AND trans.local = :local'
        );


        $objQueryExistModel = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $objQueryExistModel
            ->select('1')
            ->from(CarsModel::class, 'model')
            ->where('model.brand = main.id');


        $objQueryExistModel->join(
            'model',
            CarsModification::class,
            'mod',
            'mod.model = model.id');

        $objQueryExistModel->join(
            'mod',
            CarsModificationCharacteristics::class,
            'char',
            'char.event = mod.event');

        $objQueryExistModel->join(
            'char',
            CarsModificationTires::class,
            'tires',
            'tires.characteristic = char.id');


        //$qb->andWhere($qb->expr()->exists($objQueryExistModel->getDQL()));
        $dbal->andWhere('EXISTS('.$objQueryExistModel->getSQL().')');

        $dbal->orderBy('trans.name');


        $dbal->addSelect('main.id AS value');
        $dbal->addSelect('trans.name AS attr');
        $dbal->addSelect('info.review AS option');

        return $dbal
            ->enableCache('reference-cars', 86400)
            ->fetchAllHydrate(CarsBrandUid::class);
    }


    public function getDetailCollectionByTires(): ?array
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        //        $select = sprintf('new %s(main.id, trans.name, info.review)', CarsBrandUid::class);
        //        $qb->addSelect($select);

        $dbal->from(CarsBrand::class, 'main');

        $dbal
            ->addSelect('info.review AS brand_review')
            ->addSelect('info.url AS brand_url')
            ->leftJoin(
                'main',
                CarsBrandInfo::class,
                'info',
                'info.brand = main.id'
            );

        $dbal
            ->addSelect('trans.name AS brand_name')
            ->leftJoin(
                'main',
                CarsBrandTrans::class,
                'trans',
                'trans.event = main.event AND trans.local = :local'
            );


        $dbal
            ->addSelect('modify.mod_date AS last_modify')
            ->leftJoin(
                'main',
                CarsBrandModify::class,
                'modify',
                'modify.event = main.event'
            );


        $dbal
            //->addSelect('logo.name AS image_name')
            ->addSelect('logo.ext AS image_ext')
            ->addSelect('logo.cdn AS image_cdn')
            ->leftJoin(
                'main',
                CarsBrandLogo::class,
                'logo',
                'logo.event = main.event'
            );

        $dbal->addSelect("
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


        $objQueryExistModel = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $objQueryExistModel
            ->select('1')
            ->from(CarsModel::class, 'model')
            ->where('model.brand = main.id');

        $objQueryExistModel->join(
            'model',
            CarsModification::class,
            'mod',
            'mod.model = model.id');

        $objQueryExistModel->join(
            'mod',
            CarsModificationCharacteristics::class,
            'char',
            'char.event = mod.event');

        $objQueryExistModel->join(
            'char',
            CarsModificationTires::class,
            'tires',
            'tires.characteristic = char.id');


        //$qb->andWhere($qb->expr()->exists($objQueryExistModel->getDQL()));
        $dbal->andWhere('EXISTS('.$objQueryExistModel->getSQL().')');

        $dbal->orderBy('trans.name');

        return $dbal
            ->enableCache('reference-cars', 86400)
            ->fetchAllAssociative();
    }


    public function getCollection(): Generator
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal->from(CarsBrand::class, 'main');

        $dbal->leftJoin(
            'main',
            CarsBrandTrans::class,
            'trans',
            'trans.event = main.event AND trans.local = :local'
        );

        $dbal->orderBy('trans.name');


        $dbal->addSelect('main.id AS value');
        $dbal->addSelect('trans.name AS attr');

        return $dbal
            ->enableCache('reference-cars', 86400)
            ->fetchAllHydrate(CarsBrandUid::class);
    }
}