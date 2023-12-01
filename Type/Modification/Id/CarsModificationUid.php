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

namespace BaksDev\Reference\Cars\Type\Modification\Id;

use App\Kernel;
use BaksDev\Core\Type\UidType\Uid;
use Symfony\Component\Uid\AbstractUid;


final class CarsModificationUid extends Uid
{
    public const TEST = '018ad881-2b7b-7337-9430-60deaf00f496';

    public const TYPE = 'cars_mod';

    private mixed $attr;
    /**
     * @var mixed|null
     */
    private mixed $option;
    /**
     * @var mixed|null
     */
    private mixed $property;
    /**
     * @var mixed|null
     */
    private mixed $characteristic;

    public function __construct(
        AbstractUid|string|null $value = null,
        mixed $attr = null,
        mixed $option = null,
        mixed $property = null,
        mixed $characteristic = null,
    )
    {
        parent::__construct($value);

        $this->attr = $attr;
        $this->option = $option;
        $this->property = $property;
        $this->characteristic = $characteristic;
    }

    /**
     * Attr
     */
    public function getAttr(): mixed
    {
        return $this->attr;
    }

    /**
     * Option
     */
    public function getOption(): mixed
    {
        return $this->option;
    }

    /**
     * Property
     */
    public function getProperty(): mixed
    {
        return $this->property;
    }

    /**
     * Characteristic
     */
    public function getCharacteristic(): mixed
    {
        return $this->characteristic;
    }

}