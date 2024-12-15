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

namespace BaksDev\Reference\Cars\Repository\Models\CarsModelsChoice;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
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

final readonly class CarsModelsChoice implements CarsModelsChoiceInterface
{

    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}

    public function getCollectionByTires(mixed $brand): Generator
    {

        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal
            ->from(CarsModel::class, 'main')
            ->andWhere('main.brand = :brand')
            ->setParameter('brand', new CarsBrandUid($brand), CarsBrandUid::TYPE);

        $dbal->leftJoin(
            'main',
            CarsModelTrans::class,
            'trans',
            'trans.event = main.event AND trans.local = :local'
        );

        $dbal->leftJoin(
            'main',
            CarsModelEvent::class,
            'event',
            'event.id = main.event'
        );

        $dbal->leftJoin(
            'main',
            CarsModelInfo::class,
            'info',
            'info.event = main.event'
        );


        $objQueryExistModel = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $objQueryExistModel
            ->select('1')
            ->from(CarsModification::class, 'mod')
            ->where('mod.model = main.id');

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

        $dbal->andWhere('EXISTS('.$objQueryExistModel->getSQL().')');

        $dbal->orderBy('trans.name');

        $dbal
            ->addSelect('main.id AS value')
            ->addSelect('trans.name AS attr')
            ->addSelect('event.code AS option');

        return $dbal
            ->enableCache('reference-cars', 86400)
            ->fetchAllHydrate(CarsModelUid::class);
    }


    public function getDetailModelsExistTires(mixed $brand): ?array
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal
            ->addSelect('main.id')
            ->from(CarsModel::class, 'main')
            ->andWhere('main.brand = :brand')
            ->setParameter('brand', new CarsBrandUid($brand), CarsBrandUid::TYPE);


        $dbal
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
            ->addSelect('modify.mod_date AS last_modify')
            ->leftJoin(
                'main',
                CarsModelModify::class,
                'modify',
                'modify.event = main.event'
            );

        $dbal->addSelect(
            "
			CASE
			   WHEN image.name IS NOT NULL THEN
					CONCAT ( '/upload/".$dbal->table(CarsModelImage::class)."' , '/', image.name)
			   ELSE NULL
			END AS image_name
		"
        );
        $dbal
            ->addSelect('image.ext AS image_ext')
            ->addSelect('image.cdn AS image_cdn')
            ->leftJoin(
                'main',
                CarsModelImage::class,
                'image',
                'image.event = main.event'
            );


        $dbal
            ->addSelect('trans.name')
            ->leftJoin(
                'main',
                CarsModelTrans::class,
                'trans',
                'trans.event = main.event AND trans.local = :local'
            );

        $dbal
            ->addSelect('info.url')
            ->addSelect('info.review')
            ->leftJoin(
                'main',
                CarsModelInfo::class,
                'info',
                'info.event = main.event'
            );


        $objQueryExistModel = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $objQueryExistModel
            ->select('1')
            ->from(CarsModification::class, 'mod')
            ->where('mod.model = main.id');

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

        $dbal->andWhere('EXISTS('.$objQueryExistModel->getSQL().')');

        $dbal->orderBy('event.year_from', 'DESC');
        $dbal->addOrderBy('trans.name', 'ASC');

        return $dbal
            ->enableCache('reference-cars', 86400)
            ->fetchAllAssociative();

    }


    public function getCollection(mixed $brand): Generator
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal
            ->from(CarsModel::class, 'main')
            ->andWhere('main.brand = :brand')
            ->setParameter('brand', new CarsBrandUid($brand), CarsBrandUid::TYPE);

        $dbal->leftJoin(
            'main',
            CarsModelTrans::class,
            'trans',
            'trans.event = main.event AND trans.local = :local'
        );

        $dbal->leftJoin(
            'main',
            CarsModelEvent::class,
            'event',
            'event.id = main.event'
        );

        $dbal->leftJoin(
            'main',
            CarsModelInfo::class,
            'info',
            'info.event = main.event'
        );

        $dbal->orderBy('trans.name');

        $dbal
            ->addSelect('main.id AS value')
            ->addSelect('trans.name AS attr')
            ->addSelect('event.code AS option');

        return $dbal
            ->enableCache('reference-cars', 86400)
            ->fetchAllHydrate(CarsModelUid::class);
    }
}