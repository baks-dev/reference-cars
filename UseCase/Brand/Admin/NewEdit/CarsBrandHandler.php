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

namespace BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit;

use BaksDev\Files\Resources\Upload\Image\ImageUploadInterface;


use BaksDev\Reference\Cars\Entity\Brand;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CarsBrandHandler
{
	private EntityManagerInterface $entityManager;
	private ImageUploadInterface $imageUpload;

	//private TranslatorInterface $translator;
	private ValidatorInterface $validator;
	private LoggerInterface $logger;
	//private RequestStack $request;
	
	public function __construct(
		EntityManagerInterface $entityManager,
		ImageUploadInterface $imageUpload,

		//TranslatorInterface $translator,
		
		ValidatorInterface $validator,
		LoggerInterface $logger,
		//RequestStack $request,
	
	)
	{
		$this->entityManager = $entityManager;
		$this->imageUpload = $imageUpload;
		

		//$this->translator = $translator;
		$this->validator = $validator;
		$this->logger = $logger;
		//$this->request = $request;
	}
	
	public function handle(
		Brand\Event\CarsBrandEventInterface $command,
	) : string|Brand\CarsBrand
	{
		/* Валидация */
		$errors = $this->validator->validate($command);
		
		if(count($errors) > 0)
		{
			$uniqid = uniqid('', false);
			$errorsString = (string) $errors;
			$this->logger->error($uniqid.': '.$errorsString);
			return $uniqid;
		}
		
		
		if($command->getEvent())
		{
			$EventRepo = $this->entityManager->getRepository(Brand\Event\CarsBrandEvent::class)->find(
				$command->getEvent()
			);
			
			if($EventRepo === null)
			{
				$uniqid = uniqid('', false);
				$errorsString = sprintf(
					'Not found %s by id: %s',
					Brand\Event\CarsBrandEvent::class,
					$command->getEvent()
				);
				$this->logger->error($uniqid.': '.$errorsString);
				
				return $uniqid;
			}
			
			$Event = $EventRepo->cloneEntity();
			
		} else
		{
			$Event = new Brand\Event\CarsBrandEvent();
			$this->entityManager->persist($Event);
		}
		
		
		$this->entityManager->clear();
		
		
		
		/** @var Brand\CarsBrand $CarsBrand */
		if($Event->getBrand())
		{
			$CarsBrand = $this->entityManager->getRepository(Brand\CarsBrand::class)->findOneBy(
				['event' => $command->getEvent()]
			);
			
			if(empty($CarsBrand))
			{
				$uniqid = uniqid('', false);
				$errorsString = sprintf(
					'Not found %s by event: %s',
					Brand\CarsBrand::class,
					$command->getEvent()
				);
				$this->logger->error($uniqid.': '.$errorsString);
				
				return $uniqid;
			}

		}
		else
		{
			
			$CarsBrand = new Brand\CarsBrand();
			$CarsBrand->setEvent($Event);
			$this->entityManager->persist($CarsBrand);
			$Event->setBrand($CarsBrand);
		}
		
		
		$Event->setEntity($command);
		
		$this->entityManager->persist($Event);
		
		
		/** Загружаем файл обложки */
		/** @var Logo\CarsBrandLogoDTO $Logo */
		$Logo = $command->getLogo();
		
		if($Logo->file !== null)
		{
			$CarsBrandLogo = $Logo->getEntityUpload();
			$this->imageUpload->upload($Logo->file, $CarsBrandLogo);
		}
		
		/* присваиваем событие корню */
		$CarsBrand->setEvent($Event);
		$this->entityManager->flush();
		
		return $CarsBrand;
	}
    
}