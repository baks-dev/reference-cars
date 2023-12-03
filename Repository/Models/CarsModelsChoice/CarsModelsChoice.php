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

namespace BaksDev\Reference\Cars\Repository\Models\CarsModelsChoice;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEvent;
use BaksDev\Reference\Cars\Entity\Model\Image\CarsModelImage;
use BaksDev\Reference\Cars\Entity\Model\Info\CarsModelInfo;
use BaksDev\Reference\Cars\Entity\Model\Modify\CarsModelModify;
use BaksDev\Reference\Cars\Entity\Model\Trans\CarsModelTrans;
use BaksDev\Reference\Cars\Entity\Modification\CarsModification;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\Tires\CarsModificationTires;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use Generator;

final class CarsModelsChoice implements CarsModelsChoiceInterface
{
    private DBALQueryBuilder $DBALQueryBuilder;

    public function __construct(
        DBALQueryBuilder $DBALQueryBuilder,
    )
    {
        $this->DBALQueryBuilder = $DBALQueryBuilder;
    }

    public function getCollectionByTires(mixed $brand): Generator
    {


        $qb = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $qb
            ->from(CarsModel::TABLE, 'main')
            ->andWhere('main.brand = :brand')
            ->setParameter('brand', new CarsBrandUid($brand), CarsBrandUid::TYPE);

        $qb->leftJoin(
            'main',
            CarsModelTrans::TABLE,
            'trans',
            'trans.event = main.event AND trans.local = :local'
        );

        $qb->leftJoin(
            'main',
            CarsModelEvent::TABLE,
            'event',
            'event.id = main.event'
        );

        $qb->leftJoin(
            'main',
            CarsModelInfo::TABLE,
            'info',
            'info.event = main.event'
        );


        $objQueryExistModel = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $objQueryExistModel
            ->select('1')
            ->from(CarsModification::TABLE, 'mod')
            ->where('mod.model = main.id');

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

        $qb->andWhere('EXISTS('.$objQueryExistModel->getSQL().')');

        $qb->orderBy('trans.name');

        $qb
            ->addSelect('main.id AS value')
            ->addSelect('trans.name AS attr')
            ->addSelect('event.code AS option');

        return $qb
            ->enableCache('reference-cars', 86400)
            ->fetchAllHydrate(CarsModelUid::class);
    }


    public function getDetailModelsExistTires(mixed $brand): ?array
    {
        $qb = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $qb
            ->addSelect('main.id')
            ->from(CarsModel::TABLE, 'main')
            ->andWhere('main.brand = :brand')
            ->setParameter('brand', new CarsBrandUid($brand), CarsBrandUid::TYPE);


        $qb
            ->addSelect('event.code')
            ->addSelect('event.year_from')
            ->addSelect('event.year_to')
            ->leftJoin(
                'main',
                CarsModelEvent::TABLE,
                'event',
                'event.id = main.event'
            );

        $qb
            ->addSelect('modify.mod_date AS last_modify')
            ->leftJoin(
                'main',
                CarsModelModify::TABLE,
                'modify',
                'modify.event = main.event'
            );

        $qb->addSelect(
            "
			CASE
			   WHEN image.name IS NOT NULL THEN
					CONCAT ( '/upload/".CarsModelImage::TABLE."' , '/', image.name)
			   ELSE NULL
			END AS image_name
		"
        );
        $qb
            ->addSelect('image.ext AS image_ext')
            ->addSelect('image.cdn AS image_cdn')
            ->leftJoin(
                'main',
                CarsModelImage::TABLE,
                'image',
                'image.event = main.event'
            );


        $qb
            ->addSelect('trans.name')
            ->leftJoin(
                'main',
                CarsModelTrans::TABLE,
                'trans',
                'trans.event = main.event AND trans.local = :local'
            );

        $qb
            ->addSelect('info.url')
            ->addSelect('info.review')
            ->leftJoin(
                'main',
                CarsModelInfo::TABLE,
                'info',
                'info.event = main.event'
            );


        $objQueryExistModel = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $objQueryExistModel
            ->select('1')
            ->from(CarsModification::TABLE, 'mod')
            ->where('mod.model = main.id');

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

        $qb->andWhere('EXISTS('.$objQueryExistModel->getSQL().')');

        $qb->orderBy('event.year_from', 'DESC');
        $qb->addOrderBy('trans.name', 'ASC');

        return $qb
            ->enableCache('reference-cars', 86400)
            ->fetchAllAssociative();

    }


    public function getCollection(mixed $brand): Generator
    {
        $qb = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $qb
            ->from(CarsModel::TABLE, 'main')
            ->andWhere('main.brand = :brand')
            ->setParameter('brand', new CarsBrandUid($brand), CarsBrandUid::TYPE);

        $qb->leftJoin(
            'main',
            CarsModelTrans::TABLE,
            'trans',
            'trans.event = main.event AND trans.local = :local'
        );

        $qb->leftJoin(
            'main',
            CarsModelEvent::TABLE,
            'event',
            'event.id = main.event'
        );

        $qb->leftJoin(
            'main',
            CarsModelInfo::TABLE,
            'info',
            'info.event = main.event'
        );

        $qb->orderBy('trans.name');

        $qb
            ->addSelect('main.id AS value')
            ->addSelect('trans.name AS attr')
            ->addSelect('event.code AS option');

        return $qb
            ->enableCache('reference-cars', 86400)
            ->fetchAllHydrate(CarsModelUid::class);
    }
}