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

use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEvent;
use BaksDev\Reference\Cars\Messenger\Model\CarsModelMessage;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use DomainException;


final class CarsModelHandler extends AbstractHandler
{

    public function handle(CarsModelDTO $command, ?CarsModelUid $uid = null): string|CarsModel
    {

        /** Валидация DTO  */
        $this->validatorCollection->add($command);

        $this->main = new CarsModel($uid);
        $this->event = new CarsModelEvent();

        try
        {
            $command->getEvent() ? $this->preUpdate($command, true) : $this->prePersist($command);
        }
        catch(DomainException $errorUniqid)
        {
            return $errorUniqid->getMessage();
        }

        /** Загружаем файл обложки */
        if(method_exists($command, 'getImage'))
        {
            $Image = $command->getImage();

            if($Image->file !== null)
            {
                $CarsModelImage = $this->event->getUploadImage();
                $this->imageUpload->upload($Image->file, $CarsModelImage);
            }
        }

        if($command->getBrand())
        {
            $this->main->setBrand($command->getBrand());
        }

        /** Валидация всех объектов */
        if($this->validatorCollection->isInvalid())
        {
            return $this->validatorCollection->getErrorUniqid();
        }

        $this->entityManager->flush();

        /* Отправляем сообщение в шину */
        $this->messageDispatch->dispatch(
            message: new CarsModelMessage($this->main->getId(), $this->main->getEvent(), $command->getEvent()),
            transport: 'reference-cars'
        );

        return $this->main;

    }

}