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

namespace BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\Image;

use BaksDev\Reference\Cars\Entity\Model\Image\CarsModelImageInterface;
use Symfony\Component\HttpFoundation\File\File;

/** @see CarsModelImage */
final class CarsModelImageDTO implements CarsModelImageInterface
{
    public ?File $file = null;

    private ?string $name = null;

    private ?string $ext = null;

    private bool $cdn = false;

    private int $size = 0;


    /* NAME */

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }


    /* EXT */

    public function getExt(): ?string
    {
        return $this->ext;
    }

    public function setExt(?string $ext): self
    {
        $this->ext = $ext;
        return $this;
    }


    /* CDN */

    public function getCdn(): bool
    {
        return $this->cdn;
    }

    public function setCdn(bool $cdn): self
    {
        $this->cdn = $cdn;
        return $this;
    }

    /**
     * Size
     */
    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;
        return $this;
    }

}
