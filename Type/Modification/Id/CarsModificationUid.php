<?php
/*
 *  Copyright 2023-2024.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Cars\Type\Modification\Id;

use App\Kernel;
use BaksDev\Core\Type\UidType\Uid;
use Symfony\Component\Uid\AbstractUid;


final class CarsModificationUid extends Uid
{
    public const string TEST = '018ad881-2b7b-7337-9430-60deaf00f496';

    public const string TYPE = 'cars_mod';

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
        AbstractUid|self|string|null $value = null,
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