<?php
/*
 * Copyright (c) 2023.  Baks.dev <admin@baks.dev>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace BaksDev\Reference\Cars\UseCase\Modification\NewEdit\Characteristic\Disc;

use BaksDev\Reference\Cars\Entity\Modification\Characteristics\Disc\CarsModificationDiscInterface;

final class CarsModificationDiscDTO implements CarsModificationDiscInterface
{

    /* Спереди / front */

    /** Вылет (ET) */
    private ?string $et = null;


    /** Диаметр 18″ */
    private ?string $diameter = null;

    /** Ширина 9.5″ */
    private ?string $width = null;


    /** Вылет (ET) */
    public function getEt(): ?string
    {
        return $this->et;
    }

    public function setEt(mixed $et): void
    {
        $this->et = $et ? (string) $et : null;
    }


    /** Диаметр 18″ */
    public function getDiameter(): ?string
    {
        return $this->diameter;
    }

    public function setDiameter(mixed $diameter): void
    {
        $this->diameter = $diameter ? (string) $diameter : null;
    }


    /** Ширина 9.5″ */
    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(mixed $width): void
    {
        $this->width = $width ? (string) $width : null;
    }
}