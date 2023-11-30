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

namespace BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit;


use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEventInterface;
use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;
use BaksDev\Reference\Cars\Type\Brand\Event\CarsBrandEventUid;
use BaksDev\Core\Type\Locale\Locale;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class CarsBrandDTO implements CarsBrandEventInterface
{
	/** Идентификатор события */
	#[Assert\Uuid]
	private ?CarsBrandEventUid $id = null;
	
	/** Перевод */
	#[Assert\Valid]
	private ArrayCollection $translate;
	
	/** Логотип */
	#[Assert\Valid]
	private Logo\CarsBrandLogoDTO $logo;

    #[Assert\Valid]
    private Info\CarsBrandInfoDTO $info;
	
	
	public function __construct()
	{
		$this->translate = new ArrayCollection();
		$this->logo = new Logo\CarsBrandLogoDTO();

        $this->info = new Info\CarsBrandInfoDTO();
		
	}

	public function getEvent() : ?CarsBrandEventUid
	{
		return $this->id;
	}
	
	/* TRANSLATE */

	public function getTranslate() : ArrayCollection
	{
		/* Вычисляем расхождение и добавляем неопределенные локали */
		foreach(Locale::diffLocale($this->translate) as $locale)
		{
			$TransFormDTO = new Trans\CarsBrandTransDTO();
			$TransFormDTO->setLocal($locale);
			$this->addTranslate($TransFormDTO);
		}
		
		return $this->translate;
	}
	
	
	public function addTranslate(Trans\CarsBrandTransDTO $translate) : void
	{
        if(empty($translate->getLocal()->getLocalValue()))
        {
            return;
        }

		$this->translate->add($translate);
	}
	
	public function removeTranslate(Trans\CarsBrandTransDTO $translate) : void
	{
		$this->translate->removeElement($translate);
	}
	

	
	/* LOGO */
	
	public function getLogo() : Logo\CarsBrandLogoDTO
	{
		return $this->logo;
	}
	
	public function setLogo(Logo\CarsBrandLogoDTO $logo) : void
	{
		$this->logo = $logo;
	}

    /**
     * Info
     */
    public function getInfo(): Info\CarsBrandInfoDTO
    {
        return $this->info;
    }

    public function setInfo(Info\CarsBrandInfoDTO $info): void
    {
        $this->info = $info;
    }

}