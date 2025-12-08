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

namespace BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit;

use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use BaksDev\Reference\Cars\Messenger\Brand\CarsBrandMessage;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;

final class CarsBrandHandler extends AbstractHandler
{
    public function handle(CarsBrandDTO $command, ?CarsBrandUid $uid = null): string|CarsBrand
    {

        $this
            ->setCommand($command)
            ->preEventPersistOrUpdate(CarsBrand::class, CarsBrandEvent::class);

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

        $this->flush();

        /* Отправляем сообщение в шину */
        $this->messageDispatch->dispatch(
            message: new CarsBrandMessage($this->main->getId(), $this->main->getEvent(), $command->getEvent()),
            transport: 'reference-cars'
        );

        return $this->main;
    }
}