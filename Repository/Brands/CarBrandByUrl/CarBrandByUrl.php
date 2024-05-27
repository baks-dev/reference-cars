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

namespace BaksDev\Reference\Cars\Repository\Brands\CarBrandByUrl;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Info\CarsBrandInfo;
use BaksDev\Reference\Cars\Entity\Brand\Logo\CarsBrandLogo;
use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;
use BaksDev\Reference\Cars\Type\Brand\Event\CarsBrandEventUid;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class CarBrandByUrl implements CarBrandByUrlInterface
{
    private DBALQueryBuilder $DBALQueryBuilder;
    private string $CDN_HOST;

    public function __construct(
        #[Autowire(env: 'CDN_HOST')] string $CDN_HOST,
        DBALQueryBuilder $DBALQueryBuilder,
    )
    {
        $this->DBALQueryBuilder = $DBALQueryBuilder;
        $this->CDN_HOST = $CDN_HOST;
    }


    private ?array $brand = null;


    /**
     * Возвращает бренд по url
     */
    public function getBrand(string $url): self
    {
        $qb = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $qb->select('info.url');

        $qb->from(CarsBrandInfo::TABLE, 'info')
            ->where('info.url = :url')
            ->setParameter('url', $url);

        $qb
            ->addSelect('main.id')
            ->addSelect('main.event')
            ->join(
                'info',
                CarsBrand::TABLE,
                'main',
                'main.id = info.brand'
            );

        $qb
            ->addSelect('trans.name')
            ->leftJoin(
                'main',
                CarsBrandTrans::TABLE,
                'trans',
                'trans.event = main.event AND trans.local = :local'
            );

        $qb
            ->addSelect('logo.name AS logo_image')
            ->addSelect('logo.ext AS logo_ext')
            ->addSelect('logo.cdn AS logo_cdn')
            ->leftJoin(
                'main',
                CarsBrandLogo::TABLE,
                'logo',
                'logo.event = main.event'
            );

        $this->brand = $qb
            ->enableCache('reference-cars', 86400)
            ->fetchAssociative();

        return $this;
    }


    public function getId(): CarsBrandUid
    {
        return new CarsBrandUid($this->brand['id']);
    }

    public function getEvent(): CarsBrandEventUid
    {
        return new CarsBrandEventUid($this->brand['event']);
    }

    public function getUrl(): string
    {
        return $this->brand['url'];
    }

    public function getName(): string
    {
        return $this->brand['name'];
    }

    public function getLogo(): ?string
    {
        if($this->brand['logo_ext'])
        {
            return
                ($this->brand['logo_cdn'] ? $this->CDN_HOST : '').
                '/upload/'.CarsBrandLogo::TABLE.'/'.$this->brand['logo_image'].
                ($this->brand['logo_cdn'] ? '/small.' : '/image.').$this->brand['logo_ext'];
        }

        return null;
    }
}