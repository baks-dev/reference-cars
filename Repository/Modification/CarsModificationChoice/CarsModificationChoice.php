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

namespace BaksDev\Reference\Cars\Repository\Modification\CarsModificationChoice;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Reference\Cars\Entity\Modification\CarsModification;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\Motor\CarsModificationMotor;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\Tires\CarsModificationTires;
use BaksDev\Reference\Cars\Entity\Modification\Event\CarsModificationEvent;
use BaksDev\Reference\Cars\Entity\Modification\Info\CarsModificationInfo;
use BaksDev\Reference\Cars\Entity\Modification\Modify\CarsModificationModify;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use BaksDev\Reference\Cars\Type\Modification\Characteris\CarsModificationCharacteristicsUid;
use BaksDev\Reference\Cars\Type\Modification\Id\CarsModificationUid;
use Generator;

final class CarsModificationChoice implements CarsModificationChoiceInterface
{
    private DBALQueryBuilder $DBALQueryBuilder;

    public function __construct(
        DBALQueryBuilder $DBALQueryBuilder,
    )
    {
        $this->DBALQueryBuilder = $DBALQueryBuilder;
    }

    public function getCollectionByTires(CarsModelUid $model): Generator
    {
        $qb = $this
            ->DBALQueryBuilder
            ->createQueryBuilder(self::class);

        // $select = sprintf('new %s(char.id, event.modification, char.model, char.from, char.to)', CarsModificationCharacteristicsUid::class);

        // $qb->select($select);
        //$qb->addSelect('char');

        $qb
            ->from(CarsModification::TABLE, 'main')
            ->andWhere('main.model = :model')
            ->setParameter('model', $model, CarsModelUid::TYPE);

        $qb->leftJoin(
            'main',
            CarsModificationEvent::TABLE,
            'event',
            'event.id = main.event'
        );

        $qb->leftJoin(
            'main',
            CarsModificationCharacteristics::TABLE,
            'char',
            'char.event = main.event'
        );

        $objQueryExistModel = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $objQueryExistModel
            ->select('1')
            ->from(CarsModificationTires::TABLE, 'tires')
            ->where('tires.characteristic = char.id');


        $qb->andWhere('EXISTS('.$objQueryExistModel->getSQL().')');

        $qb->orderBy('event.modification');
        $qb->addOrderBy('char.model');

        $qb
            ->addSelect('char.id AS value')
            ->addSelect('event.modification AS attr')
            ->addSelect('char.model AS option')
            ->addSelect('char.year_from AS property')
            ->addSelect('char.year_to AS characteristic');


        return $qb
            ->enableCache('reference-cars', 86400)
            ->fetchAllHydrate(CarsModificationCharacteristicsUid::class);
    }



    public function getCollection(CarsModelUid $model): Generator
    {
        $qb = $this
            ->DBALQueryBuilder
            ->createQueryBuilder(self::class);

        // $select = sprintf('new %s(char.id, event.modification, char.model, char.from, char.to)', CarsModificationCharacteristicsUid::class);

        // $qb->select($select);
        //$qb->addSelect('char');

        $qb
            ->from(CarsModification::TABLE, 'main')
            ->andWhere('main.model = :model')
            ->setParameter('model', $model, CarsModelUid::TYPE);

        $qb->leftJoin(
            'main',
            CarsModificationEvent::TABLE,
            'event',
            'event.id = main.event'
        );

        $qb->leftJoin(
            'main',
            CarsModificationCharacteristics::TABLE,
            'char',
            'char.event = main.event'
        );


        $qb->orderBy('event.modification');
        $qb->addOrderBy('char.model');

        $qb
            ->addSelect('char.id AS value')
            ->addSelect('event.modification AS attr')
            ->addSelect('char.model AS option')
            ->addSelect('char.year_from AS property')
            ->addSelect('char.year_to AS characteristic');


        return $qb
            ->enableCache('reference-cars', 86400)
            ->fetchAllHydrate(CarsModificationCharacteristicsUid::class);
    }


    public function getDetailCollectionByTires(CarsModelUid $model): ?array
    {
        $qb = $this
            ->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        //$select = sprintf('new %s(char.id, event.modification, char.model, char.from, char.to)', CarsModificationCharacteristicsUid::class);

        //$qb->select($select);
        //$qb->addSelect('char');

        $qb->addSelect('char.id');

        $qb
            ->from(CarsModification::TABLE, 'main')
            ->andWhere('main.model = :model')
            ->setParameter('model', $model, CarsModelUid::TYPE);

        $qb
            ->addSelect('event.modification AS modification')
            ->leftJoin(
                'main',
                CarsModificationEvent::TABLE,
                'event',
                'event.id = main.event'
            );

        $qb
            ->addSelect('modify.mod_date AS last_modify')
            ->leftJoin(
                'main',
                CarsModificationModify::TABLE,
                'modify',
                'modify.event = main.event'
            );

        $qb
            ->addSelect('info.url')
            ->leftJoin(
                'main',
                CarsModificationInfo::TABLE,
                'info',
                'info.modification = main.id'
            );

        $qb
            ->addSelect('char.id AS characteristic_id')
            ->addSelect('char.model AS characteristic_model')
            ->addSelect('char.year_from AS characteristic_from')
            ->addSelect('char.year_to AS characteristic_to')
            ->leftJoin(
                'main',
                CarsModificationCharacteristics::TABLE,
                'char',
                'char.event = main.event'
            );

        $qb
            ->addSelect('motor.fuel AS motor_fuel')
            ->addSelect('motor.engine AS motor_engine')
            ->addSelect('motor.power AS motor_power')
            ->addSelect('motor.drive AS motor_drive')
            ->leftJoin(
                'char',
                CarsModificationMotor::TABLE,
                'motor',
                'motor.characteristic = char.id'
            );


        $objQueryExistModel = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $objQueryExistModel
            ->select('1')
            ->from(CarsModificationTires::TABLE, 'tires')
            ->where('tires.characteristic = char.id');

        //$qb->andWhere($qb->expr()->exists($objQueryExistModel->getDQL()));
        $qb->andWhere('EXISTS('.$objQueryExistModel->getSQL().')');

        $qb->orderBy('event.modification');
        $qb->addOrderBy('char.model');

        return $qb
            //->enableCache('reference-cars', 86400)
            ->fetchAllAssociative();
    }
}