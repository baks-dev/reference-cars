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

namespace BaksDev\Reference\Cars\Type\Model\Id;

use App\Kernel;
use BaksDev\Core\Type\UidType\Uid;
use Symfony\Component\Uid\AbstractUid;

final class CarsModelUid extends Uid
{
    public const TEST = '0188a9a2-dc29-7e10-b271-8fe114b15544';

    public const TYPE = 'cars_model';

    private mixed $attr;
    /**
     * @var mixed|null
     */
    private mixed $option;

    public function __construct(
        AbstractUid|string|null $value = null,
        mixed $attr = null,
        mixed $option = null,
    )
    {
        parent::__construct($value);
        $this->attr = $attr;
        $this->option = $option;
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
}