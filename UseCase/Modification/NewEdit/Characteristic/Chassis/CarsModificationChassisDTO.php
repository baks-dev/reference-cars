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

namespace BaksDev\Reference\Cars\UseCase\Modification\NewEdit\Characteristic\Chassis;

use BaksDev\Reference\Cars\Entity\Modification\Characteristics\Chassis\CarsModificationChassisInterface;
use Symfony\Component\Validator\Constraints as Assert;

/** @see CarsModificationChassis */
final class CarsModificationChassisDTO implements CarsModificationChassisInterface
{
	
	/** Количество крепежей */
	private ?int $number = null;
	
	/** Диаметр (PCD):*/
	private ?string $pcd = null;
	
	/** Ступичное отверстие (DIA):*/
	private ?string $dia = null;
	
	/** Крепёж */
	private ?string $fastener = null;
	
	
	
	
	/** Крепление колеса (PCD):*/
	public function getPcd() : ?string
	{
		return $this->pcd;
	}

	public function setPcd(string|float|null $pcd) : void
	{
		$this->pcd = $pcd ? (string) $pcd : null;
	}
	
	
	
	
	
	/** Ступичное отверстие (DIA):*/
	public function getDia() : ?string
	{
		return $this->dia;
	}

	public function setDia(string|float|null $dia) : void
	{
		$this->dia = $dia ? (string) $dia : null;
	}
	
	
	
	
	
	/** Крепление колеса (PCD):*/
	public function getFastener() : ?string
	{
		return $this->fastener;
	}

	public function setFastener(?string $fastener) : void
	{
		$this->fastener = $fastener;
	}
	
	
	
	/** Количество крепежей */
	public function getNumber() : ?int
	{
		return $this->number;
	}

	public function setNumber(?int $number) : void
	{
		$this->number = $number;
	}

	
	
	
}