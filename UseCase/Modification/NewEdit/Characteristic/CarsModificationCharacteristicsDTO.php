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

namespace BaksDev\Reference\Cars\UseCase\Modification\NewEdit\Characteristic;

use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristicsInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** @see CarsModificationCharacteristics */
final class CarsModificationCharacteristicsDTO implements CarsModificationCharacteristicsInterface
{
    /** Модель двигателя */
    private ?string $model = null;

    /** Год начала выпуска модификации  */
    #[Assert\NotBlank]
    private int $from;

    /** Год окончания выпуска модификации (null - по наст. время) */
    private ?int $to = null;


    /** Двигатель */
    #[Assert\Valid]
    private Motor\CarsModificationMotorDTO $motor;

    /** Шаси */
    #[Assert\Valid]
    private Chassis\CarsModificationChassisDTO $chassis;


    /** Шины */
    #[Assert\Valid]
    private ArrayCollection $tire;

    /** Диски */
    #[Assert\Valid]
    private ArrayCollection $disc;


    public function __construct()
    {

        $this->tire = new ArrayCollection();
        $this->disc = new ArrayCollection();

        $this->motor = new Motor\CarsModificationMotorDTO();
        $this->chassis = new Chassis\CarsModificationChassisDTO();
    }

    /** Модель двигателя */
    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): void
    {
        $this->model = $model;
    }

    /** Год начала выпуска модификации  */
    public function getFrom(): int
    {
        return $this->from;
    }

    public function setFrom(int $from): void
    {
        $this->from = $from;
    }

    /** Год окончания выпуска модификации (null - по наст. время) */
    public function getTo(): ?int
    {
        return $this->to;
    }

    public function setTo(?int $to): void
    {
        $this->to = $to;
    }

    /** Двигатель */
    public function getMotor(): Motor\CarsModificationMotorDTO
    {
        return $this->motor;
    }

    public function setMotor(Motor\CarsModificationMotorDTO $motor): void
    {
        $this->motor = $motor;
    }

    /** Шаси */
    public function getChassis(): ?Chassis\CarsModificationChassisDTO
    {
        if(
            $this->chassis->getNumber() === null &&
            $this->chassis->getPcd() === null &&
            $this->chassis->getDia() === null &&
            $this->chassis->getFastener() === null
        )
        {
            return null;
        }

        return $this->chassis;
    }

    public function setChassis(Chassis\CarsModificationChassisDTO $chassis): void
    {
        $this->chassis = $chassis;
    }


    /** Шины */
    public function getTire(): ArrayCollection
    {
        return $this->tire;
    }

    public function addTire(Tire\CarsModificationTiresDTO $tire): void
    {
        if(!$this->tire->contains($tire))
        {
            $this->tire->add($tire);
        }
    }

    public function removeTire(Tire\CarsModificationTiresDTO $tire): void
    {
        $this->tire->removeElement($tire);
    }


    /** Диски */
    public function getDisc(): ArrayCollection
    {
        return $this->disc;
    }

    public function addDisc(Disc\CarsModificationDiscDTO $disc): void
    {
        if(!$this->disc->contains($disc))
        {
            $this->disc->add($disc);
        }
    }

    public function removeDisc(Disc\CarsModificationDiscDTO $disc): void
    {
        $this->disc->removeElement($disc);
    }


}