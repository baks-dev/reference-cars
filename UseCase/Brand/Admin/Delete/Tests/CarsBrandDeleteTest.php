<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Cars\UseCase\Brand\Admin\Delete\Tests;

use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\CarsBrandDTO;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Logo\CarsBrandLogoDTO;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Tests\CarsBrandEditTest;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Trans\CarsBrandTransDTO;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('reference-cars')]
final class CarsBrandDeleteTest extends KernelTestCase
{
    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass(): void
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

    #[DependsOnClass(CarsBrandEditTest::class)]
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

        /** @var CarsBrandTransDTO $CarsBrandTrans */
        $CarsBrandTrans = $CarsBrandDTO->getTranslate()->current();

        self::assertEquals('ru', (string) $CarsBrandTrans->getLocal());
        self::assertEquals('BrandEdit', $CarsBrandTrans->getName());
        self::assertEquals('DescriptionEdit', $CarsBrandTrans->getDescription());


        /** @var CarsBrandLogoDTO $CarsBrandLogoDTO */
        $CarsBrandLogoDTO = $CarsBrandDTO->getLogo();
        self::assertEquals('testEdit', $CarsBrandLogoDTO->getName());
        self::assertEquals('webp', $CarsBrandLogoDTO->getExt());
        self::assertTrue($CarsBrandLogoDTO->getCdn());

        //        /** @var CarsBrandHandler $CarsBrandHandler */
        //        $CarsBrandHandler = self::getContainer()->get(CarsBrandHandler::class);
        //        $handle = $CarsBrandHandler->handle($CarsBrandDTO);
        //
        //        self::assertTrue(($handle instanceof CarsBrand), $handle.': Ошибка CarsBrand');

        $em->clear();
        //$em->close();

    }

}