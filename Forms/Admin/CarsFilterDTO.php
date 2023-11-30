<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Cars\Forms\Admin;

use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use BaksDev\Reference\Cars\Type\Modification\Id\CarsModificationUid;

final class CarsFilterDTO
{
    private ?CarsBrandUid $brand = null;
    private ?CarsModelUid $model = null;
    private ?CarsModificationUid $modification = null;

    /**
     * Brand
     */
    public function getBrand(): ?CarsBrandUid
    {
        return $this->brand;
    }

    public function setBrand(?CarsBrandUid $brand): self
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * Model
     */
    public function getModel(): ?CarsModelUid
    {
        return $this->model;
    }

    public function setModel(?CarsModelUid $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Modification
     */
    public function getModification(): ?CarsModificationUid
    {
        return $this->modification;
    }

    public function setModification(?CarsModificationUid $modification): self
    {
        $this->modification = $modification;
        return $this;
    }

}