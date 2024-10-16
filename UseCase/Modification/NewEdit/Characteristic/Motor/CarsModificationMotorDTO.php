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

namespace BaksDev\Reference\Cars\UseCase\Modification\NewEdit\Characteristic\Motor;

use BaksDev\Reference\Cars\Entity\Modification\Characteristics\Motor\CarsModificationMotorInterface;
use Symfony\Component\Validator\Constraints as Assert;

/** @see CarsModificationMotor */
final class CarsModificationMotorDTO implements CarsModificationMotorInterface
{
    /** Тип двигателя (бензин/дизль ...) */
    private ?string $fuel = null;

    /** Объем двигателя (л) */
    private ?string $engine = null;

    /** Мощность двигателя, л.с. */
    private ?string $power;

    /** Привод */
    private ?string $drive;


    /** Тип двигателя (бензин/дизль ...) */
    public function getFuel(): ?string
    {
        return $this->fuel;
    }

    public function setFuel(?string $fuel): void
    {
        $this->fuel = $fuel;
    }

    /** Объем двигателя (л) */
    public function getEngine(): ?string
    {
        return $this->engine;
    }

    /** Объем двигателя (л) */
    public function setEngine(?string $engine): void
    {
        $this->engine = $engine;
    }

    /** Мощность двигателя, л.с. */
    public function getPower(): ?string
    {
        return $this->power;
    }

    public function setPower(?string $power): void
    {
        $this->power = $power;
    }

    /** Привод */
    public function getDrive(): ?string
    {
        return $this->drive;
    }


    public function setDrive(?string $drive): void
    {
        $this->drive = $drive;
    }

}