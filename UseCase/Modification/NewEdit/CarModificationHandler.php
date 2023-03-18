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

namespace BaksDev\Reference\Cars\UseCase\Modification\NewEdit;

use BaksDev\Reference\Cars\Entity\Modification as Entity;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CarModificationHandler
{
	private EntityManagerInterface $entityManager;
	private ValidatorInterface $validator;
	private LoggerInterface $logger;
	
	public function __construct(
		EntityManagerInterface $entityManager,
		ValidatorInterface $validator,
		LoggerInterface $logger,
	)
	{
		$this->entityManager = $entityManager;
		$this->validator = $validator;
		$this->logger = $logger;
	}
	
	public function handle(
		CarModificationDTO $command,
		//?UploadedFile $cover = null
	) : string|Entity\CarsModification
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
			$EventRepo = $this->entityManager->getRepository(Entity\Event\CarsModificationEvent::class)->find(
				$command->getEvent()
			);
			
			if($EventRepo === null)
			{
				$uniqid = uniqid('', false);
				$errorsString = sprintf(
					'Not found %s by id: %s',
					Entity\Event\CarsModificationEvent::class,
					$command->getEvent()
				);
				$this->logger->error($uniqid.': '.$errorsString);
				
				return $uniqid;
			}
			
			$Event = $EventRepo->cloneEntity();
			
		} else
		{
			$Event = new Entity\Event\CarsModificationEvent();
			$this->entityManager->persist($Event);
		}
		
		
		$this->entityManager->clear();
		
		
		/** @var Entity\CarsModification $CarsModification */
		if($Event->getMain())
		{
			$CarsModification = $this->entityManager->getRepository(Entity\CarsModification::class)->findOneBy(
				['event' => $command->getEvent()]
			);
			
			if(empty($CarsModification))
			{
				$uniqid = uniqid('', false);
				$errorsString = sprintf(
					'Not found %s by event: %s',
					Entity\CarsModification::class,
					$command->getEvent()
				);
				$this->logger->error($uniqid.': '.$errorsString);
				
				return $uniqid;
			}
			
		} else
		{
			
			$CarsModification = new Entity\CarsModification();
			$this->entityManager->persist($CarsModification);
			$Event->setMain($CarsModification);
		}
		
		
		$Event->setEntity($command);
		$this->entityManager->persist($Event);
		
		/* присваиваем событие корню */
		$CarsModification->setEvent($Event);
		$this->entityManager->flush();
		
		return $CarsModification;
	}
}