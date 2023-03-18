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

namespace BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit;

use BaksDev\Files\Resources\Upload\Image\ImageUploadInterface;


use BaksDev\Reference\Cars\Entity\Model;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Logo;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CarsModelHandler
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
		Model\Event\CarsModelEventInterface $command,
		//?UploadedFile $cover = null
	) : string|Model\CarsModel
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
			$EventRepo = $this->entityManager->getRepository(Model\Event\CarsModelEvent::class)->find(
				$command->getEvent()
			);
			
			if($EventRepo === null)
			{
				$uniqid = uniqid('', false);
				$errorsString = sprintf(
					'Not found %s by id: %s',
					Model\Event\CarsModelEvent::class,
					$command->getEvent()
				);
				$this->logger->error($uniqid.': '.$errorsString);
				
				return $uniqid;
			}
			
			$Event = $EventRepo->cloneEntity();
			
		}
		else
		{
			$Event = new Model\Event\CarsModelEvent();
			$this->entityManager->persist($Event);
		}
		
		
		$this->entityManager->clear();
		
		
		
		/** @var Model\CarsModel $CarsModel */
		if($Event->getMain())
		{
			$CarsModel = $this->entityManager->getRepository(Model\CarsModel::class)->findOneBy(
				['event' => $command->getEvent()]
			);
			
			if(empty($CarsModel))
			{
				$uniqid = uniqid('', false);
				$errorsString = sprintf(
					'Not found %s by event: %s',
					Model\CarsModel::class,
					$command->getEvent()
				);
				$this->logger->error($uniqid.': '.$errorsString);
				
				return $uniqid;
			}

		}
		else
		{
			
			$CarsModel = new Model\CarsModel();
			$this->entityManager->persist($CarsModel);
			$Event->setMain($CarsModel);
		}
		
		
		$Event->setEntity($command);
		
		$this->entityManager->persist($Event);
		
		/* Загружаем файл изображения */
		/** @var Image\CarsModelImageDTO $Image */
		$Image = $command->getImage();
		if($Image->file !== null)
		{
			$CarsModelImage = $Event->getUploadClass();
			$this->imageUpload->upload('cars_model_dir', $Image->file, $CarsModelImage);
		}
		
		/* присваиваем событие корню */
		$CarsModel->setEvent($Event);
		$this->entityManager->flush();
		
		
		return $CarsModel;
	}
    
}