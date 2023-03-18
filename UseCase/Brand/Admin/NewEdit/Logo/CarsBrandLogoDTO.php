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

namespace BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Logo;

use BaksDev\Reference\Cars\Entity\Brand\Logo\CarsBrandLogoInterface;
use App\Module\Users\Profile\UserProfile\Type\Event\UserProfileEventUid;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

final class CarsBrandLogoDTO implements CarsBrandLogoInterface
{
    /** Логотип */
    #[Assert\File(
      maxSize         : '1024k',
      mimeTypes       : [
        'image/png',
        'image/gif',
        'image/jpeg',
        'image/pjpeg',
        'image/webp',
      ],
      mimeTypesMessage: 'Please upload a valid file'
    )]
    public ?File $file = null;
    
    
    private ?string $name = null;
    
    private ?string $ext = null;
    
    private bool $cdn = false;
	
	#[Assert\Uuid]
    private ?UserProfileEventUid $dir = null;

    
    /* NAME */

    public function getName() : ?string
    {
        return $this->name;
    }
	
    /* EXT */
    public function getExt() : ?string
    {
        return $this->ext;
    }
	
    /* CDN */

    public function isCdn() : bool
    {
        return $this->cdn;
    }
	
    /* DIR */

    public function getDir() : ?UserProfileEventUid
    {
        return $this->dir;
    }
	
}