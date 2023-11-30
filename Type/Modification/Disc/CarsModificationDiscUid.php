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

namespace BaksDev\Reference\Cars\Type\Modification\Disc;

use App\Kernel;
use BaksDev\Core\Type\UidType\Uid;
use Symfony\Component\Uid\AbstractUid;

final class CarsModificationDiscUid extends Uid
{
    public const TEST = '018ad881-093c-7848-87db-f40796909888';

    public const TYPE = 'cars_modification_disc';

}