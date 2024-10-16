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

namespace BaksDev\Reference\Cars\UseCase\Modification\NewEdit\Characteristic\Tire;

use BaksDev\Reference\Cars\Entity\Modification\Characteristics\Tires\CarsModificationTiresInterface;

final class CarsModificationTiresDTO implements CarsModificationTiresInterface
{

    /** Размер */
    private ?int $size = null;

    /** Профиль */
    private ?int $profile = null;

    /** Радиус */
    private ?int $radius = null;


    /** Размер */
    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): void
    {
        $this->size = $size;
    }


    /** Профиль */
    public function getProfile(): ?int
    {
        return $this->profile;
    }

    public function setProfile(?int $profile): void
    {
        $this->profile = $profile;
    }


    /** Радиус */
    public function getRadius(): ?int
    {
        return $this->radius;
    }

    public function setRadius(?int $radius): void
    {
        $this->radius = $radius;
    }

}