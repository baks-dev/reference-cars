<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit;

use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEvent;
use BaksDev\Reference\Cars\Messenger\Model\CarsModelMessage;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;


final class CarsModelHandler extends AbstractHandler
{

    public function handle(CarsModelDTO $command, ?CarsModelUid $uid = null): string|CarsModel
    {
        $this
            ->setCommand($command)
            ->preEventPersistOrUpdate(new CarsModel($uid), CarsModelEvent::class);

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

        $this->flush();

        /* Отправляем сообщение в шину */
        $this->messageDispatch->dispatch(
            message: new CarsModelMessage($this->main->getId(), $this->main->getEvent(), $command->getEvent()),
            transport: 'reference-cars'
        );

        return $this->main;

    }

}