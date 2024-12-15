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

namespace BaksDev\Reference\Cars\Repository\Modification\CarsModificationExists;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Cars\Entity\Modification\CarsModification;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use BaksDev\Reference\Cars\Type\Modification\Id\CarsModificationUid;

final readonly class CarsModificationExists implements CarsModificationExistsInterface
{

    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Метод проверят, имеется ли модификация с таким идентификатором
     */
    public function isExistById(CarsModelUid $model, CarsModificationUid $modification): bool
    {
        $qb = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $qb
            ->from(CarsModification::class, 'main')
            ->where('main.id = :modification')
            ->andWhere('main.model = :model')
            ->setParameter('modification', $modification)
            ->setParameter('model', $model);

        return $qb->fetchExist();
    }
}