<?php
/*
 *  Copyright 2023-2025.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

namespace BaksDev\Reference\Cars\UseCase\Modification\NewEdit;

use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Reference\Cars\Entity\Modification\CarsModification;
use BaksDev\Reference\Cars\Entity\Modification\Event\CarsModificationEvent;
use BaksDev\Reference\Cars\Type\Modification\Id\CarsModificationUid;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CarModificationHandler extends AbstractHandler
{

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
    //            $this->logger->error(sprintf('%s: %s', $uniqid, $errors), [self::class.':'.__LINE__]);
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
    //            $this->logger->error(sprintf('%s: %s', $uniqid, $errors), [self::class.':'.__LINE__]);
    //
    //            return $uniqid;
    //        }
    //
    //        $this->entityManager->flush();
    //
    //        return $CarsModification;
    //    }
}