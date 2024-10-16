<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 */

namespace BaksDev\Reference\Cars\Entity\Brand\Logo;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Files\Resources\Upload\UploadEntityInterface;
use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use BaksDev\Reference\Cars\Type\Brand\Event\CarsBrandEventUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* Обложка раздела */


#[ORM\Entity]
#[ORM\Table(name: 'cars_brand_logo')]
class CarsBrandLogo extends EntityEvent implements UploadEntityInterface
{
    public const TABLE = 'cars_brand_logo';

    /** Связь на событие */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'logo', targetEntity: CarsBrandEvent::class)]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private CarsBrandEvent $event;

    /** Название файла */
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING)]
    private string $name;

    /** Расширение файла */
    #[Assert\NotBlank]
    #[Assert\Choice(['png', 'gif', 'jpg', 'jpeg', 'webp'])]
    #[ORM\Column(type: Types::STRING)]
    private string $ext;

    /** Размер файла */
    #[Assert\NotBlank]
    #[Assert\Range(max: 2097152)] // 1024 * 1024 * 2
    #[ORM\Column(type: Types::INTEGER)]
    private int $size = 0;

    /** Файл загружен на CDN */
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $cdn = false;


    public function __construct(CarsBrandEvent $event)
    {
        $this->event = $event;
    }

    public function __toString(): string
    {
        return (string) $this->event;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CarsBrandLogoInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {
        /* Если размер файла нулевой - не заполняем сущность */
        if(empty($dto->file) && empty($dto->getName()))
        {
            return false;
        }

        //        if(!empty($dto->file))
        //        {
        //            $dto->setEntityUpload($this);
        //        }

        if($dto instanceof CarsBrandLogoInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function updFile(string $name, string $ext, int $size): void
    {
        $this->name = $name;
        $this->ext = $ext;
        $this->size = $size;
        //$this->dir = $this->event->getId();
        $this->cdn = false;
    }


    public function updCdn(?string $ext = null): void
    {
        if($ext)
        {
            $this->ext = $ext;
        }

        $this->cdn = true;
    }


    public function getId(): CarsBrandEventUid
    {
        return $this->event->getId();
    }

    /**
     * Ext
     */
    public function getExt(): string
    {
        return $this->ext;
    }




    //    public function getUploadDir(): object
    //    {
    //        return $this->event->getId();
    //    }
    //
    //
    //    public static function getDirName(): string
    //    {
    //        return CarsBrandEventUid::class;
    //    }
    //
    //    public function getDir(): CarsBrandEventUid
    //    {
    //        return $this->dir;
    //    }

}
