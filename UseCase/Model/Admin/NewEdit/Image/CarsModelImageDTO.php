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
use BaksDev\Reference\Cars\Type\Model\Event\CarsModelEventUid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

final class CarsModelImageDTO implements CarsModelImageInterface
{
	
	/** Файл изображения */
	#[Assert\File(
		maxSize: '1024k',
		mimeTypes: [
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
	private ?CarsModelEventUid $dir = null;
	
	/** Сущность для загрузки и обновления файла  */
	private mixed $entityUpload;
	
	
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
	
	public function getDir() : ?CarsModelEventUid
	{
		return $this->dir;
	}
	
	
	/** Сущность для загрузки и обновления файла  */
	
	public function getEntityUpload() : mixed
	{
		return $this->entityUpload;
	}
	
	
	public function setEntityUpload(mixed $entityUpload) : void
	{
		$this->entityUpload = $entityUpload;
	}
	
}