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

namespace BaksDev\Reference\Cars\Forms\Filter;

use Symfony\Component\Validator\Constraints as Assert;

final class CarsFilterDTO
{
    #[Assert\NotBlank]
    private mixed $brand = null;

    #[Assert\NotBlank]
    private mixed $model = null;

    #[Assert\NotBlank]
    private mixed $modification = null;

    private mixed $season = null;

    private mixed $studs = false;

    /**
     * Brand
     */
    public function getBrand(): mixed
    {
        return $this->brand;
    }

    public function setBrand(mixed $brand): self
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * Model
     */
    public function getModel(): mixed
    {
        return $this->model;
    }

    public function setModel(mixed $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Modification
     */
    public function getModification(): mixed
    {
        return $this->modification;
    }

    public function setModification(mixed $modification): self
    {
        $this->modification = $modification;
        return $this;
    }

    /**
     * Season
     */
    public function getSeason(): mixed
    {
        return $this->season;
    }

    public function setSeason(mixed $season): self
    {
        $this->season = $season;
        return $this;
    }

    /**
     * Studs
     */
    public function getStuds(): mixed
    {
        return $this->studs;
    }

    public function setStuds(mixed $studs): self
    {
        $this->studs = $studs;
        return $this;
    }


}