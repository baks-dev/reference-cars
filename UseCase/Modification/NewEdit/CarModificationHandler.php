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

use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Reference\Cars\Entity\Modification\CarsModification;
use BaksDev\Reference\Cars\Entity\Modification\Event\CarsModificationEvent;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use BaksDev\Reference\Cars\Type\Modification\Id\CarsModificationUid;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CarModificationHandler extends AbstractHandler
{
    //    private EntityManagerInterface $entityManager;
    //    private ValidatorInterface $validator;
    //    private LoggerInterface $logger;
    //
    //    public function __construct(
    //        EntityManagerInterface $entityManager,
    //        ValidatorInterface $validator,
    //        LoggerInterface $logger,
    //    )
    //    {
    //        $this->entityManager = $entityManager;
    //        $this->validator = $validator;
    //        $this->logger = $logger;
    //    }
    public function handle(CarModificationDTO $command, ?CarsModificationUid $uid = null): string|CarsModification
    {
        /** Валидация DTO  */
        $this->validatorCollection->add($command);

        $this->main = new CarsModification($uid);
        $this->event = new CarsModificationEvent();

        try
        {
            $command->getEvent() ? $this->preUpdate($command, true) : $this->prePersist($command);
        }
        catch(DomainException $errorUniqid)
        {
            return $errorUniqid->getMessage();
        }

        //        /* Загружаем файл изображения */
        //        /** @var Image\CarsModelImageDTO $Image */
        //        $Image = $command->getImage();
        //        if($Image->file !== null)
        //        {
        //            $CarsModelImage = $Event->getUploadClass();
        //            $this->imageUpload->upload('cars_model_dir', $Image->file, $CarsModelImage);
        //        }

        if(!$command->getEvent())
        {
            $this->main->setModel($command->getModel());
        }

        /** Валидация всех объектов */
        if($this->validatorCollection->isInvalid())
        {
            return $this->validatorCollection->getErrorUniqid();
        }

        $this->entityManager->flush();

        //        /* Отправляем сообщение в шину */
        //        $this->messageDispatch->dispatch(
        //            message: new CarsModelMessage($this->main->getId(), $this->main->getEvent(), $command->getEvent()),
        //            transport: 'reference-cars'
        //        );

        return $this->main;
    }

    //    public function OLDhandle(CarModificationDTO $command): string|Entity\CarsModification
    //    {
    //        /* Валидация */
    //        $errors = $this->validator->validate($command);
    //
    //        if(count($errors) > 0)
    //        {
    //            /** Ошибка валидации */
    //            $uniqid = uniqid('', false);
    //            $this->logger->error(sprintf('%s: %s', $uniqid, $errors), [__FILE__.':'.__LINE__]);
    //
    //            return $uniqid;
    //        }
    //
    //
    //        if($command->getEvent())
    //        {
    //            $EventRepo = $this->entityManager->getRepository(Entity\Event\CarsModificationEvent::class)->find(
    //                $command->getEvent()
    //            );
    //
    //            if($EventRepo === null)
    //            {
    //                $uniqid = uniqid('', false);
    //                $errorsString = sprintf(
    //                    'Not found %s by id: %s',
    //                    Entity\Event\CarsModificationEvent::class,
    //                    $command->getEvent()
    //                );
    //                $this->logger->error($uniqid.': '.$errorsString);
    //
    //                return $uniqid;
    //            }
    //
    //            $EventRepo->setEntity($command);
    //            $EventRepo->setEntityManager($this->entityManager);
    //            $Event = $EventRepo->cloneEntity();
    //        }
    //        else
    //        {
    //            $Event = new Entity\Event\CarsModificationEvent();
    //            $Event->setEntity($command);
    //            $this->entityManager->persist($Event);
    //        }
    //
    //        //        $this->entityManager->clear();
    //        //        $this->entityManager->persist($Event);
    //
    //
    //        /** @var Entity\CarsModification $CarsModification */
    //        if($Event->getMain())
    //        {
    //            $CarsModification = $this->entityManager->getRepository(Entity\CarsModification::class)
    //                ->findOneBy(['event' => $command->getEvent()]);
    //
    //            if(empty($CarsModification))
    //            {
    //                $uniqid = uniqid('', false);
    //                $errorsString = sprintf(
    //                    'Not found %s by event: %s',
    //                    Entity\CarsModification::class,
    //                    $command->getEvent()
    //                );
    //                $this->logger->error($uniqid.': '.$errorsString);
    //
    //                return $uniqid;
    //            }
    //
    //        }
    //        else
    //        {
    //
    //            $CarsModification = new Entity\CarsModification();
    //            $this->entityManager->persist($CarsModification);
    //            $Event->setMain($CarsModification);
    //        }
    //
    //        /* присваиваем событие корню */
    //        $CarsModification->setEvent($Event);
    //
    //
    //        /**
    //         * Валидация Event
    //         */
    //
    //        $errors = $this->validator->validate($Event);
    //
    //        if(count($errors) > 0)
    //        {
    //            /** Ошибка валидации */
    //            $uniqid = uniqid('', false);
    //            $this->logger->error(sprintf('%s: %s', $uniqid, $errors), [__FILE__.':'.__LINE__]);
    //
    //            return $uniqid;
    //        }
    //
    //        $this->entityManager->flush();
    //
    //        return $CarsModification;
    //    }
}