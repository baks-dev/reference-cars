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

use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use BaksDev\Reference\Cars\Messenger\Brand\CarsBrandMessage;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use DomainException;

final class CarsBrandHandler extends AbstractHandler
{
    public function handle(CarsBrandDTO $command, ?CarsBrandUid $uid = null): string|CarsBrand
    {

        /** Валидация DTO  */
        $this->validatorCollection->add($command);

        $this->main = new CarsBrand($uid);
        $this->event = new CarsBrandEvent();

        try
        {
            $command->getEvent() ? $this->preUpdate($command, true) : $this->prePersist($command);
        }
        catch(DomainException $errorUniqid)
        {
            return $errorUniqid->getMessage();
        }


        /** Загружаем файл обложки */
        if(method_exists($command, 'getLogo'))
        {
            $Logo = $command->getLogo();

            if($Logo->file !== null)
            {
                $CarsBrandLogo = $this->event->getUploadLogo();
                $this->imageUpload->upload($Logo->file, $CarsBrandLogo);
            }
        }

        /** Валидация всех объектов */
        if($this->validatorCollection->isInvalid())
        {
            return $this->validatorCollection->getErrorUniqid();
        }

        $this->entityManager->flush();

        /* Отправляем сообщение в шину */
        $this->messageDispatch->dispatch(
            message: new CarsBrandMessage($this->main->getId(), $this->main->getEvent(), $command->getEvent()),
            transport: 'reference-cars'
        );

        return $this->main;
    }
}