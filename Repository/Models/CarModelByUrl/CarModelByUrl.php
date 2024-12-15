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

namespace BaksDev\Reference\Cars\Repository\Models\CarModelByUrl;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Info\CarsBrandInfo;
use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEvent;
use BaksDev\Reference\Cars\Entity\Model\Image\CarsModelImage;
use BaksDev\Reference\Cars\Entity\Model\Info\CarsModelInfo;
use BaksDev\Reference\Cars\Entity\Model\Trans\CarsModelTrans;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class CarModelByUrl implements CarModelByUrlInterface
{

    private ?array $model;

    public function __construct(
        #[Autowire(env: 'CDN_HOST')] private readonly string $CDN_HOST,
        private readonly DBALQueryBuilder $DBALQueryBuilder,
    ) {}

    /** Метод возвращает модель по URL */
    public function getModel(string $brand, string $model): self
    {
        $qb = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $qb->select('brand_info.url AS brand_url');

        $qb->from(CarsBrandInfo::class, 'brand_info')
            ->where('brand_info.url = :brand')
            ->setParameter('brand', $brand);

        $qb
            //            ->addSelect('main.id AS brand_id')
            ->addSelect('model_info.url AS model_url')
            ->join(
                'brand_info',
                CarsModelInfo::class,
                'model_info',
                'model_info.url = :model'
            )->setParameter('model', $model);


        $qb
            ->addSelect('brand_main.id AS brand_id')
            ->addSelect('brand_main.event AS brand_event')
            ->join(
                'brand_info',
                CarsBrand::class,
                'brand_main',
                'brand_main.id = brand_info.brand'
            );


        $qb
            ->addSelect('brand_trans.name AS brand_name')
            ->leftJoin(
                'brand_main',
                CarsBrandTrans::class,
                'brand_trans',
                'brand_trans.event = brand_main.event AND brand_trans.local = :local'
            );


        /** Модель */

        $qb
            ->addSelect('model_main.id AS model_id')
            ->addSelect('model_main.event AS model_event')
            ->join(
                'model_info',
                CarsModel::class,
                'model_main',
                'model_main.id = model_info.model AND model_main.brand = brand_main.id'
            );

        $qb
            ->addSelect('model_event.code AS model_code')
            ->addSelect('model_event.year_from AS model_from')
            ->addSelect('model_event.year_to AS model_to')
            ->leftJoin(
                'model_main',
                CarsModelEvent::class,
                'model_event',
                'model_event.id = model_main.event'
            );


        $qb
            ->addSelect('model_trans.name AS model_name')
            ->leftJoin(
                'model_main',
                CarsModelTrans::class,
                'model_trans',
                'model_trans.event = model_main.event AND model_trans.local = :local'
            );

        $qb
            ->addSelect('model_image.name AS image_name')
            ->addSelect('model_image.ext AS image_ext')
            ->addSelect('model_image.cdn AS image_cdn')
            ->leftJoin(
                'model_main',
                CarsModelImage::class,
                'model_image',
                'model_image.event = model_main.event'
            );

        $this->model = $qb
            // ->enableCache('reference-cars', 3600)
            ->fetchAssociative();

        return $this;
    }


    /** Модель */

    public function getBrandId(): CarsBrandUid
    {
        return new CarsBrandUid($this->model['brand_id']);
    }

    public function getBrandName(): string
    {
        return $this->model['brand_name'];
    }

    public function getBrandUrl(): string
    {
        return $this->model['brand_url'];
    }


    /** Модель */

    public function getModelId(): CarsModelUid
    {
        return new CarsModelUid($this->model['model_id']);
    }

    public function getModelName(): string
    {
        return $this->model['model_name'];
    }

    public function getModelCode(): string
    {
        return $this->model['model_code'];
    }

    public function getModelUrl(): string
    {
        return $this->model['model_url'];
    }

    public function getModelImage(): ?string
    {
        $TABLE = $this->DBALQueryBuilder->table(CarsModelImage::class);

        if($this->model['image_ext'])
        {
            return
                ($this->model['image_cdn'] ? $this->CDN_HOST : '').
                '/upload/'.$TABLE.'/'.$this->model['image_name'].
                ($this->model['image_cdn'] ? '/small.' : '/image.').$this->model['image_ext'];
        }

        return null;
    }

    public function getModelFrom(): int
    {
        return $this->model['model_from'];
    }

    public function getModelTo(): ?int
    {
        return $this->model['model_to'] ?: (int) date("Y");
    }

}