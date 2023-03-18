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


//use BaksDev\Files\Resources\Upload\UploadEntityInterface;
use BaksDev\Files\Resources\Upload\UploadEntityInterface;
use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use App\Module\Users\Profile\UserProfile\Entity\Event\Event;
use App\Module\Users\Profile\UserProfile\Entity\Event\UserProfileEvent;
use App\Module\Users\Profile\UserProfile\Type\Event\UserProfileEventUid;
use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Entity\EntityState;
use BaksDev\Reference\Cars\Type\Brand\Event\CarsBrandEventUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;


/* Обложка раздела */

#[ORM\Entity]
#[ORM\Table(name: 'cars_brand_logo')]
class CarsBrandLogo extends EntityEvent implements UploadEntityInterface
{
    public const TABLE = 'cars_brand_logo';

    /** Связь на событие */
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'logo', targetEntity: CarsBrandEvent::class)]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private CarsBrandEvent $event;
    
    /** Название директории по идентификатору события */
    #[ORM\Column(type: CarsBrandEventUid::TYPE)]
	private CarsBrandEventUid $dir;
    
    /** Название файла */
    #[ORM\Column(type: Types::STRING, length: 100)]
	private string $name;
    
    /** Расширение файла */
    #[ORM\Column(type: Types::STRING, length: 64)]
	private string $ext;
    
    /** Размер файла */
    #[ORM\Column(type: Types::INTEGER)]
    private int $size = 0;
    
    /** Файл загружен на CDN */
    #[ORM\Column(type: Types::BOOLEAN)]
	private bool $cdn = false;
    

    public function __construct(CarsBrandEvent $event) { $this->event = $event; }
	
	
    public function getDto($dto) : mixed
    {
        if($dto instanceof CarsBrandLogoInterface)
        {
            return parent::getDto($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    

    public function setEntity($dto) : mixed
    {
        /* Если размер файла нулевой - не заполняем сущность */
        if(
          (empty($dto->file) && empty($dto->getName())) ||
          (!empty($dto->file) && empty($dto->getName()))
        )
        {
            return false;
        }

        if($dto instanceof CarsBrandLogoInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    
    public function updFile(string $name, string $ext, int $size) : void
    {
        $this->name = $name;
        $this->ext = $ext;
        $this->size = $size;
        $this->dir = $this->event->getId();
        $this->cdn = false;
    }

    public function updCdn(string $ext): void
    {
        $this->ext = $ext;
        $this->cdn = true;
    }

    public function getId() : CarsBrandEventUid
    {
        return $this->event->getId();
    }
    
    public function getUploadDir() : object
    {
        return $this->event->getId();
    }
    
}