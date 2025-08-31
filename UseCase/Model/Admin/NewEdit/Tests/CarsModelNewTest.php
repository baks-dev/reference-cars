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

namespace BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\Tests;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Core\Type\Locale\Locales\Ru;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEvent;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use BaksDev\Reference\Cars\Type\Model\Type\CarsModelClass;
use BaksDev\Reference\Cars\Type\Model\Type\CarsModelClass\CarsModelClassA;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\CarsModelDTO;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\CarsModelHandler;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\Image\CarsModelImageDTO;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\Trans\CarsModelTransDTO;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('reference-cars')]
final class CarsModelNewTest extends KernelTestCase
{

    public static function setUpBeforeClass(): void
    {
        $CarsModelClassA = new CarsModelClassA();

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


    public function testUseCase()
    {
        $CarsModelDTO = new CarsModelDTO();


        $CarsModelDTO->setClass(CarsModelClassA::class);
        self::assertInstanceOf(CarsModelClass::class, $CarsModelDTO->getClass());


        $CarsModelDTO->setCode('code');
        self::assertEquals('code', $CarsModelDTO->getCode());

        $CarsModelDTO->setFrom(2023);
        self::assertEquals(2023, $CarsModelDTO->getFrom());
        $CarsModelDTO->setFrom('2023');
        self::assertEquals(2023, $CarsModelDTO->getFrom());


        $CarsModelDTO->setTo(2023);
        self::assertEquals(2023, $CarsModelDTO->getTo());
        $CarsModelDTO->setTo('2023');
        self::assertEquals(2023, $CarsModelDTO->getTo());
        $CarsModelDTO->setTo(null);
        self::assertNull($CarsModelDTO->getTo());


        /** @var CarsModelTransDTO $CarsModelTransDTO */


        //$CarsBrandTrans = new CarsBrandTransDTO();

        foreach($CarsModelDTO->getTranslate() as $CarsBrandTrans)
        {
            $CarsBrandTrans->setLocal(new Locale(Ru::class));
            self::assertEquals('ru', (string) $CarsBrandTrans->getLocal());

            $CarsBrandTrans->setName('Model');
            self::assertEquals('Model', $CarsBrandTrans->getName());

            $CarsBrandTrans->setDescription('Description');
            self::assertEquals('Description', $CarsBrandTrans->getDescription());
        }


        /** @var CarsModelImageDTO $CarsModelImageDTO */

        $CarsModelImageDTO = new CarsModelImageDTO();

        $CarsModelImageDTO->setName('test');
        self::assertEquals('test', $CarsModelImageDTO->getName());

        $CarsModelImageDTO->setExt('png');
        self::assertEquals('png', $CarsModelImageDTO->getExt());

        $CarsModelImageDTO->setCdn(true);
        self::assertTrue($CarsModelImageDTO->getCdn());

        $CarsModelImageDTO->setCdn(false);
        self::assertFalse($CarsModelImageDTO->getCdn());

        $CarsModelDTO->setImage($CarsModelImageDTO);
        self::assertSame($CarsModelImageDTO, $CarsModelDTO->getImage());


        $CarsModelInfoDTO = $CarsModelDTO->getInfo();
        $CarsModelInfoDTO->setUrl('url');
        self::assertEquals('url', $CarsModelInfoDTO->getUrl());


        /** @var CarsModelHandler $CarsModelHandler */
        $CarsModelHandler = self::getContainer()->get(CarsModelHandler::class);
        $handle = $CarsModelHandler->handle($CarsModelDTO);

        self::assertTrue(($handle instanceof CarsModel), $handle.': Ошибка CarsModel');

    }
}