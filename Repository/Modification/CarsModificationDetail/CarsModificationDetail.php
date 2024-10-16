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

namespace BaksDev\Reference\Cars\Repository\Modification\CarsModificationDetail;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Info\CarsBrandInfo;
use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEvent;
use BaksDev\Reference\Cars\Entity\Model\Image\CarsModelImage;
use BaksDev\Reference\Cars\Entity\Model\Info\CarsModelInfo;
use BaksDev\Reference\Cars\Entity\Model\Trans\CarsModelTrans;
use BaksDev\Reference\Cars\Entity\Modification\CarsModification;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\Chassis\CarsModificationChassis;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\Motor\CarsModificationMotor;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\Tires\CarsModificationTires;
use BaksDev\Reference\Cars\Entity\Modification\Event\CarsModificationEvent;
use BaksDev\Reference\Cars\Entity\Modification\Info\CarsModificationInfo;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use BaksDev\Reference\Cars\Type\Modification\Characteris\CarsModificationCharacteristicsUid;

final class CarsModificationDetail implements CarsModificationDetailInterface
{
    private DBALQueryBuilder $DBALQueryBuilder;

    private ?CarsBrandUid $brand = null;

    private ?CarsModelUid $model = null;

    public function __construct(
        DBALQueryBuilder $DBALQueryBuilder,
    )
    {
        $this->DBALQueryBuilder = $DBALQueryBuilder;
    }

    public function findCarDetail(
        CarsBrandUid $brand,
        CarsModelUid $model,
        CarsModificationCharacteristicsUid $modification
    ): ?array
    {
        /** Делаем обновление статистики запросов */
        $this->brand = $brand;
        $this->model = $model;
        register_shutdown_function([$this, 'statistic'], 'throw');


        $qb = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();


        $qb
            ->from(CarsBrand::TABLE, 'brand')
            ->where('brand.id = :brand')
            ->setParameter('brand', $brand, CarsBrandUid::TYPE);

        $qb
            ->addSelect('brand_info.url AS brand_url')
            ->leftJoin(
                'brand',
                CarsBrandInfo::TABLE,
                'brand_info',
                'brand_info.brand = brand.id'
            );


        $qb
            ->addSelect('brand_trans.name as brand_name')
            ->leftJoin(
                'brand',
                CarsBrandTrans::TABLE,
                'brand_trans',
                'brand_trans.event = brand.event and brand_trans.local = :local'
            );


        $qb->leftJoin(
            'brand',
            CarsModel::TABLE,
            'model',
            'model.id = :model'
        )
            ->setParameter('model', $model, CarsModelUid::TYPE);


        $qb
            ->addSelect('model_info.url AS model_url')
            ->leftJoin(
                'model',
                CarsModelInfo::TABLE,
                'model_info',
                'model_info.model = model.id'
            );

        $qb
            ->addSelect('model_event.code AS model_code')
            ->leftJoin(
                'model',
                CarsModelEvent::TABLE,
                'model_event',
                'model_event.id = model.event'
            );


        $qb
            ->addSelect('model_trans.name as model_name')
            ->leftJoin(
                'model',
                CarsModelTrans::TABLE,
                'model_trans',
                'model_trans.event = model.event and model_trans.local = :local'
            );


        $qb
            ->addSelect("
                CASE
                   WHEN model_img.name IS NOT NULL THEN
                        CONCAT ( '/upload/".CarsModelImage::TABLE."' , '/', model_img.name)
                   ELSE NULL
                END AS model_image
		    ")
            ->addSelect('model_img.ext as model_image_ext')
            ->addSelect('model_img.cdn as model_image_cdn')
            ->leftJoin(
                'model',
                CarsModelImage::TABLE,
                'model_img',
                'model_img.event = model.event'
            );


        $qb
            ->addSelect('char.model as modification_model')
            ->addSelect('char.year_from as modification_from')
            ->addSelect('char.year_to as modification_to')
            ->leftJoin(
                'model',
                CarsModificationCharacteristics::TABLE,
                'char',
                'char.id = :modification'
            )
            ->setParameter('modification', $modification, CarsModificationCharacteristicsUid::TYPE);


        $qb
            ->addSelect('mod_event.modification as modification_name')
            ->leftJoin(
                'char',
                CarsModificationEvent::TABLE,
                'mod_event',
                'mod_event.id = char.event'
            );


        $qb
            ->addSelect('mod_motor.fuel as modification_fuel')
            ->addSelect('mod_motor.engine as modification_engine')
            ->addSelect('mod_motor.power as modification_power')
            ->addSelect('mod_motor.drive as modification_drive')
            ->addSelect('mod_motor.power as modification_power')
            ->leftJoin(
                'char',
                CarsModificationMotor::TABLE,
                'mod_motor',
                'mod_motor.characteristic = char.id'
            );


        $qb
            ->addSelect('mod_chassis.dia as modification_dia')
            ->addSelect('mod_chassis.pcd as modification_pcd')
            ->addSelect('mod_chassis.number as modification_number')
            ->addSelect('mod_chassis.fastener as modification_fastener')
            ->leftJoin(
                'char',
                CarsModificationChassis::TABLE,
                'mod_chassis',
                'mod_chassis.characteristic = char.id'
            );


        $qb
            //            ->addSelect('mod_chassis.dia as modification_dia')
            //            ->addSelect('mod_chassis.pcd as modification_pcd')
            //            ->addSelect('mod_chassis.number as modification_number')
            //            ->addSelect('mod_chassis.fastener as modification_fastener')
            ->leftJoin(
                'char',
                CarsModificationTires::TABLE,
                'mod_tire',
                'mod_tire.characteristic = char.id'
            );


        $qb->addSelect(
            "JSON_AGG
            ( DISTINCT
                
                    JSONB_BUILD_OBJECT
                    (
                        '0', mod_tire.radius,
                        'radius', mod_tire.radius,
                        'width', mod_tire.size,
                        'profile', mod_tire.profile
                    )
                
            )
			AS tire_field"
        );


        //        $qb->leftJoin(
        //            'model',
        //            CarsModification::TABLE,
        //            'mod',
        //            'mod.id = :modification'
        //        )
        //            ->setParameter('modification', $modification, CarsModificationUid::TYPE);


        $qb->allGroupByExclude();

        return $qb
            ->enableCache('reference-cars', 3600)
            ->fetchAssociative();
    }


    public function findCarDetailByUrl(
        string $brand,
        string $model,
        string $modification,
        ?string $engine = null,
        ?string $power = null

    ): ?array
    {
        $qb = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();


        $qb
            ->addSelect('brand_info.url AS brand_url')
            ->from(CarsBrandInfo::TABLE, 'brand_info')
            ->where('brand_info.url = :brand')
            ->setParameter('brand', $brand);


        $qb
            ->join(
                'brand_info',
                CarsBrand::TABLE,
                'brand',
                'brand.id = brand_info.brand'
            );

        $qb
            ->addSelect('brand_trans.name as brand_name')
            ->leftJoin(
                'brand',
                CarsBrandTrans::TABLE,
                'brand_trans',
                'brand_trans.event = brand.event and brand_trans.local = :local'
            );


        $qb
            ->addSelect('model_info.url AS model_url')
            ->join(
                'brand_info',
                CarsModelInfo::TABLE,
                'model_info',
                'model_info.url = :model'
            )
            ->setParameter('model', $model);


        $qb->join(
            'model_info',
            CarsModel::TABLE,
            'model',
            'model.id = model_info.model AND model.brand = brand.id'
        )
            ->setParameter('model', $model);


        $qb
            ->addSelect('model_event.code AS model_code')
            ->leftJoin(
                'model',
                CarsModelEvent::TABLE,
                'model_event',
                'model_event.id = model.event'
            );

        $qb
            ->addSelect('model_trans.name as model_name')
            ->leftJoin(
                'model',
                CarsModelTrans::TABLE,
                'model_trans',
                'model_trans.event = model.event and model_trans.local = :local'
            );


        $qb
            ->addSelect("
                CASE
                   WHEN model_img.name IS NOT NULL THEN
                        CONCAT ( '/upload/".CarsModelImage::TABLE."' , '/', model_img.name)
                   ELSE NULL
                END AS model_image
		    ")
            ->addSelect('model_img.ext as model_image_ext')
            ->addSelect('model_img.cdn as model_image_cdn')
            ->leftJoin(
                'model',
                CarsModelImage::TABLE,
                'model_img',
                'model_img.event = model.event'
            );


        $qb
            ->addSelect('mod_info.url AS modification_url')
            ->join(
                'model_info',
                CarsModificationInfo::TABLE,
                'mod_info',
                'mod_info.url = :modification'
            )
            ->setParameter('modification', $modification);


        $qb->join(
            'mod_info',
            CarsModification::TABLE,
            'mod',
            'mod.id = mod_info.modification'
        );


        $qb
            ->addSelect('char.model as modification_model')
            ->addSelect('char.year_from as modification_from')
            ->addSelect('char.year_to as modification_to')
            ->leftJoin(
                'mod',
                CarsModificationCharacteristics::TABLE,
                'char',
                'char.event = mod.event'
            );


        $qb
            ->addSelect('mod_motor.fuel as modification_fuel')
            ->addSelect('mod_motor.engine as modification_engine')
            ->addSelect('mod_motor.power as modification_power')
            ->addSelect('mod_motor.drive as modification_drive')
            ->addSelect('mod_motor.power as modification_power')
            ->leftOneJoin(
                'char',
                CarsModificationMotor::TABLE,
                'mod_motor',
                'mod_motor.characteristic = char.id'
                .($engine ? ' AND mod_motor.engine = :engine' : '')
                .($power ? ' AND mod_motor.power = :power' : ''),
                'characteristic'
            )
            ->setParameter('engine', $engine)
            ->setParameter('power', $power);


        $qb
            ->addSelect('mod_event.modification as modification_name')
            ->leftJoin(
                'char',
                CarsModificationEvent::TABLE,
                'mod_event',
                'mod_event.id = char.event'
            );


        //        $qb
        //            ->addSelect('mod_motor.fuel as modification_fuel')
        //            ->addSelect('mod_motor.engine as modification_engine')
        //            ->addSelect('mod_motor.power as modification_power')
        //            ->addSelect('mod_motor.drive as modification_drive')
        //            ->addSelect('mod_motor.power as modification_power')
        //            ->leftJoin(
        //                'char',
        //                CarsModificationMotor::TABLE,
        //                'mod_motor',
        //                'mod_motor.characteristic = char.id'
        //            );


        $qb
            ->addSelect('mod_chassis.dia as modification_dia')
            ->addSelect('mod_chassis.pcd as modification_pcd')
            ->addSelect('mod_chassis.number as modification_number')
            ->addSelect('mod_chassis.fastener as modification_fastener')
            ->leftJoin(
                'char',
                CarsModificationChassis::TABLE,
                'mod_chassis',
                'mod_chassis.characteristic = char.id'
            );


        $qb
            //            ->addSelect('mod_chassis.dia as modification_dia')
            //            ->addSelect('mod_chassis.pcd as modification_pcd')
            //            ->addSelect('mod_chassis.number as modification_number')
            //            ->addSelect('mod_chassis.fastener as modification_fastener')
            ->leftJoin(
                'char',
                CarsModificationTires::TABLE,
                'mod_tire',
                'mod_tire.characteristic = char.id'
            );


        $qb->addSelect(
            "JSON_AGG
            ( DISTINCT
                
                    JSONB_BUILD_OBJECT
                    (
                        '0', mod_tire.radius,
                        'radius', mod_tire.radius,
                        'width', mod_tire.size,
                        'profile', mod_tire.profile
                    )
                
            )
			AS tire_field"
        );


        //        $qb->leftJoin(
        //            'model',
        //            CarsModification::TABLE,
        //            'mod',
        //            'mod.id = :modification'
        //        )
        //            ->setParameter('modification', $modification, CarsModificationUid::TYPE);


        $qb->allGroupByExclude();

        return $qb
            ->enableCache('reference-cars', 3600)
            ->fetchAssociative();
    }

    public function statistic()
    {
        if($this->brand)
        {
            $qb = $this->DBALQueryBuilder->createQueryBuilder(self::class);

            $qb
                ->update(CarsBrandInfo::TABLE)
                ->set('review', 'review + 1')
                ->where('brand = :brand')
                ->setParameter('brand', $this->brand, CarsBrandUid::TYPE)
                ->executeQuery();
        }

        if($this->model)
        {
            $qb = $this->DBALQueryBuilder->createQueryBuilder(self::class);

            $qb
                ->update(CarsModelInfo::TABLE)
                ->set('review', 'review + 1')
                ->where('model = :model')
                ->setParameter('model', $this->model, CarsModelUid::TYPE)
                ->executeQuery();
        }
    }

}