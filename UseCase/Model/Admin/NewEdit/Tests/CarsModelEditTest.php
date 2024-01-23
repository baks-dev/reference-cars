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

namespace BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\Tests;

use BaksDev\Core\Type\Locale\Locales\Ru;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEvent;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\CarsBrandDTO;
use BaksDev\Reference\Cars\Type\Brand\Event\CarsBrandEventUid;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\CarsBrandHandler;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Logo\CarsBrandLogoDTO;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Trans\CarsBrandTransDTO;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\CarsModelDTO;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\CarsModelHandler;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\Image\CarsModelImageDTO;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\Trans\CarsModelTransDTO;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @group reference-cars
 * @group reference-cars-model
 *
 * @depends BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\Tests\CarsModelNewTest::class
 * @see CarsModelNewTest
 */
#[When(env: 'test')]
final class CarsModelEditTest extends KernelTestCase
{
    public function testUseCase()
    {
        self::bootKernel();
        $container = self::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);

        /** @var CarsModelEvent $CarsModelEvent */
        $CarsModelEvent =
            $em->createQueryBuilder()
                ->select('event')
                ->from(CarsModel::class, 'main')
                ->where('main.id = :id')
                ->setParameter('id', CarsModelUid::TEST, CarsModelUid::TYPE)
                ->leftJoin(CarsModelEvent::class, 'event', 'WITH', 'event.id = main.event')
                ->getQuery()
                ->getOneOrNullResult();

        self::assertNotNull($CarsModelEvent);

        $CarsModelDTO = new CarsModelDTO();
        $CarsModelEvent->getDto($CarsModelDTO);

        /** @var CarsBrandTransDTO $CarsBrandTrans */
        $CarsBrandTrans = $CarsModelDTO->getTranslate()->current();

        self::assertEquals('ru', (string) $CarsBrandTrans->getLocal());
        $CarsBrandTrans->setLocal(new Locale(Ru::class));

        self::assertEquals('Model', $CarsBrandTrans->getName());
        $CarsBrandTrans->setName('ModelEdit');

        self::assertEquals('Description', $CarsBrandTrans->getDescription());
        $CarsBrandTrans->setDescription('DescriptionEdit');



        /** @var CarsModelImageDTO $CarsModelImageDTO */
        $CarsModelImageDTO = $CarsModelDTO->getImage();

        self::assertEquals('test', $CarsModelImageDTO->getName());
        $CarsModelImageDTO->setName('testEdit');

        self::assertEquals('png', $CarsModelImageDTO->getExt());
        $CarsModelImageDTO->setExt('webp');


        self::assertFalse($CarsModelImageDTO->getCdn());
        $CarsModelImageDTO->setCdn(true);

        /** @var CarsModelHandler $CarsModelHandler */
        $CarsModelHandler = self::getContainer()->get(CarsModelHandler::class);
        $handle = $CarsModelHandler->handle($CarsModelDTO);

        self::assertTrue(($handle instanceof CarsModel), $handle.': Ошибка CarsModel');

        $em->clear();
        //$em->close();
    }
}