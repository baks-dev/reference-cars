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

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Core\Type\Locale\Locales\Ru;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\CarsBrandDTO;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\CarsBrandHandler;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Logo\CarsBrandLogoDTO;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Trans\CarsBrandTransDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group reference-cars
 * @group reference-cars-brand
 */
#[When(env: 'test')]
final class CarsBrandNewTest extends KernelTestCase
{

    public static function setUpBeforeClass(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $CarsBrand = $em->getRepository(CarsBrand::class)
            ->findBy(['id' => CarsBrandUid::TEST]);

        foreach($CarsBrand as $remove)
        {
            $em->remove($remove);
        }

        $CarsBrandEvent = $em->getRepository(CarsBrandEvent::class)
            ->findBy(['main' => CarsBrandUid::TEST]);

        foreach($CarsBrandEvent as $remove)
        {
            $em->remove($remove);
        }

        $em->flush();

        $em->clear();
        //$em->close();
    }


    public function testUseCase()
    {
        $CarsBrandDTO = new CarsBrandDTO();


        /** @var CarsBrandTransDTO $CarsBrandTransDTO */


        //$CarsBrandTrans = new CarsBrandTransDTO();

        foreach($CarsBrandDTO->getTranslate() as $CarsBrandTrans)
        {
            $CarsBrandTrans->setLocal(new Locale(Ru::class));
            self::assertEquals('ru', (string) $CarsBrandTrans->getLocal());

            $CarsBrandTrans->setName('Brand');
            self::assertEquals('Brand', $CarsBrandTrans->getName());

            $CarsBrandTrans->setDescription('Description');
            self::assertEquals('Description', $CarsBrandTrans->getDescription());
        }


        /** @var CarsBrandLogoDTO $CarsBrandLogoDTO */

        $CarsBrandLogoDTO = new CarsBrandLogoDTO();

        $CarsBrandLogoDTO->setName('test');
        self::assertEquals('test', $CarsBrandLogoDTO->getName());

        $CarsBrandLogoDTO->setExt('png');
        self::assertEquals('png', $CarsBrandLogoDTO->getExt());

        $CarsBrandLogoDTO->setCdn(true);
        self::assertTrue($CarsBrandLogoDTO->getCdn());

        $CarsBrandLogoDTO->setCdn(false);
        self::assertFalse($CarsBrandLogoDTO->getCdn());

        $CarsBrandDTO->setLogo($CarsBrandLogoDTO);
        self::assertSame($CarsBrandLogoDTO, $CarsBrandDTO->getLogo());


        $CarsBrandInfoDTO = $CarsBrandDTO->getInfo();
        $CarsBrandInfoDTO->setUrl('url');
        self::assertEquals('url', $CarsBrandInfoDTO->getUrl());

        /** @var CarsBrandHandler $CarsBrandHandler */
        $CarsBrandHandler = self::getContainer()->get(CarsBrandHandler::class);
        $handle = $CarsBrandHandler->handle($CarsBrandDTO);

        self::assertTrue(($handle instanceof CarsBrand), $handle.': Ошибка CarsBrand');

    }
}