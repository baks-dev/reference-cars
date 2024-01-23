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

namespace BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Tests;

use BaksDev\Core\Type\Locale\Locales\Ru;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\CarsBrandDTO;
use BaksDev\Reference\Cars\Type\Brand\Event\CarsBrandEventUid;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\CarsBrandHandler;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Logo\CarsBrandLogoDTO;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Trans\CarsBrandTransDTO;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @group reference-cars
 * @group reference-cars-brand
 *
 * @depends BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Tests\CarsBrandNewTest::class
 * @see CarsBrandNewTest
 */
#[When(env: 'test')]
final class CarsBrandEditTest extends KernelTestCase
{

    public function testUseCase()
    {
        /** @var CarsBrandTransDTO $CarsBrandTransDTO */

        self::bootKernel();
        $container = self::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);

        /** @var CarsBrandEvent $CarsBrandEvent */
        $CarsBrandEvent =
            $em->createQueryBuilder()
                ->select('event')
                ->from(CarsBrand::class, 'main')
                ->where('main.id = :id')
                ->setParameter('id', CarsBrandUid::TEST, CarsBrandUid::TYPE)
                ->leftJoin(CarsBrandEvent::class, 'event', 'WITH', 'event.id = main.event')
                ->getQuery()
                ->getOneOrNullResult();

        self::assertNotNull($CarsBrandEvent);

        $CarsBrandDTO = new CarsBrandDTO();
        $CarsBrandEvent->getDto($CarsBrandDTO);


        //$CarsBrandTrans = new CarsBrandTransDTO();

        /** @var CarsBrandTransDTO $CarsBrandTrans */
        $CarsBrandTrans = $CarsBrandDTO->getTranslate()->current();

        self::assertEquals('ru', (string) $CarsBrandTrans->getLocal());
        $CarsBrandTrans->setLocal(new Locale(Ru::class));

        self::assertEquals('Brand', $CarsBrandTrans->getName());
        $CarsBrandTrans->setName('BrandEdit');

        self::assertEquals('Description', $CarsBrandTrans->getDescription());
        $CarsBrandTrans->setDescription('DescriptionEdit');



        /** @var CarsBrandLogoDTO $CarsBrandLogoDTO */
        $CarsBrandLogoDTO = $CarsBrandDTO->getLogo();

        self::assertEquals('test', $CarsBrandLogoDTO->getName());
        $CarsBrandLogoDTO->setName('testEdit');

        self::assertEquals('png', $CarsBrandLogoDTO->getExt());
        $CarsBrandLogoDTO->setExt('webp');


        self::assertFalse($CarsBrandLogoDTO->getCdn());
        $CarsBrandLogoDTO->setCdn(true);

        /** @var CarsBrandHandler $CarsBrandHandler */
        $CarsBrandHandler = self::getContainer()->get(CarsBrandHandler::class);
        $handle = $CarsBrandHandler->handle($CarsBrandDTO);

        self::assertTrue(($handle instanceof CarsBrand), $handle.': Ошибка CarsBrand');

        $em->clear();
        //$em->close();

    }
}