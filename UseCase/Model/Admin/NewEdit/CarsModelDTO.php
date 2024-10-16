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

namespace BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEventInterface;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use BaksDev\Reference\Cars\Type\Model\Event\CarsModelEventUid;
use BaksDev\Reference\Cars\Type\Model\Type\CarsModelClass;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\Image\CarsModelImageDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** @see CarsModelEvent */
final class CarsModelDTO implements CarsModelEventInterface
{

    /** Идентификатор события */
    #[Assert\Uuid]
    private ?CarsModelEventUid $id = null;

    private CarsModelClass $class;

    /** Код автомобиля  */
    private ?string $code = null;

    /** Год начала выпуска авто  */
    #[Assert\NotBlank]
    private int $from;

    /** Год окончания выпуска авто (null - по наст. время) */
    private ?int $to = null;

    /** Обложка модели */
    #[Assert\Valid]
    private CarsModelImageDTO $image;

    /** Перевод */
    #[Assert\Valid]
    private ArrayCollection $translate;

    #[Assert\Valid]
    private Info\CarsModelInfoDTO $info;


    /** Бренд */
    #[Assert\Valid]
    private ?CarsBrandUid $brand = null;

    public function __construct()
    {
        $this->translate = new ArrayCollection();
        $this->image = new Image\CarsModelImageDTO();
        $this->info = new Info\CarsModelInfoDTO();
    }


    public function getEvent(): ?CarsModelEventUid
    {
        return $this->id;
    }


    /* CLASS */

    public function getClass(): CarsModelClass
    {
        return $this->class;
    }

    public function setClass(mixed $class): void
    {
        $this->class = new CarsModelClass($class);
    }


    /* IMAGE */

    public function getImage(): Image\CarsModelImageDTO
    {
        return $this->image;
    }

    public function setImage(Image\CarsModelImageDTO $image): void
    {
        $this->image = $image;
    }


    /* TRANSLATE */

    public function getTranslate(): ArrayCollection
    {
        /* Вычисляем расхождение и добавляем неопределенные локали */
        foreach(Locale::diffLocale($this->translate) as $locale)
        {
            $TransFormDTO = new Trans\CarsModelTransDTO();
            $TransFormDTO->setLocal($locale);
            $this->addTranslate($TransFormDTO);
        }

        return $this->translate;
    }

    public function addTranslate(Trans\CarsModelTransDTO $translate): void
    {
        if(empty($translate->getLocal()->getLocalValue()))
        {
            return;
        }

        $this->translate->add($translate);
    }

    public function removeTranslate(Trans\CarsModelTransDTO $translate): void
    {
        $this->translate->removeElement($translate);
    }


    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }


    public function getFrom(): int
    {
        return $this->from;
    }

    public function setFrom(int|string $from): void
    {
        $this->from = (int) filter_var($from, FILTER_SANITIZE_NUMBER_INT);
    }


    public function getTo(): ?int
    {
        return $this->to;
    }

    public function setTo(int|string|null $to): void
    {
        $this->to = $to ? (int) filter_var($to, FILTER_SANITIZE_NUMBER_INT) : null;
    }


    /**
     * Info
     */
    public function getInfo(): Info\CarsModelInfoDTO
    {
        return $this->info;
    }

    public function setInfo(Info\CarsModelInfoDTO $info): self
    {
        $this->info = $info;
        return $this;
    }

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
}