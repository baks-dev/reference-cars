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

namespace BaksDev\Reference\Cars\UseCase\Model\Admin\Delete\Tests;

use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEvent;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Logo\CarsBrandLogoDTO;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\CarsModelDTO;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\Tests\CarsModelEditTest;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\Trans\CarsModelTransDTO;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('reference-cars')]
final class CarsModelDeleteTest extends KernelTestCase
{
    public static function tearDownAfterClass(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $CarsBrand = $em->getRepository(CarsModel::class)
            ->findBy(['id' => CarsModelUid::TEST]);

        foreach($CarsBrand as $remove)
        {
            $em->remove($remove);
        }

        $CarsBrandEvent = $em->getRepository(CarsModelEvent::class)
            ->findBy(['main' => CarsModelUid::TEST]);

        foreach($CarsBrandEvent as $remove)
        {
            $em->remove($remove);
        }

        $em->flush();

        $em->clear();
        //$em->close();
    }

    #[DependsOnClass(CarsModelEditTest::class)]
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

        /** @var CarsModelTransDTO $CarsModelTransDTO */
        $CarsModelTransDTO = $CarsModelDTO->getTranslate()->current();

        self::assertEquals('ru', (string) $CarsModelTransDTO->getLocal());
        self::assertEquals('ModelEdit', $CarsModelTransDTO->getName());
        self::assertEquals('DescriptionEdit', $CarsModelTransDTO->getDescription());


        /** @var CarsBrandLogoDTO $CarsBrandLogoDTO */
        $CarsBrandLogoDTO = $CarsModelDTO->getImage();
        self::assertEquals('testEdit', $CarsBrandLogoDTO->getName());
        self::assertEquals('webp', $CarsBrandLogoDTO->getExt());
        self::assertTrue($CarsBrandLogoDTO->getCdn());

        $em->clear();
        //$em->close();

    }

}