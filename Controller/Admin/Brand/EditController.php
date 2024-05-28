<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
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

declare(strict_types=1);

namespace BaksDev\Reference\Cars\Controller\Admin\Brand;


use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Delivery\Entity\Delivery;
use BaksDev\Delivery\Entity\Event\DeliveryEvent;
use BaksDev\Delivery\UseCase\Admin\NewEdit\DeliveryDTO;
use BaksDev\Delivery\UseCase\Admin\NewEdit\DeliveryForm;
use BaksDev\Delivery\UseCase\Admin\NewEdit\DeliveryHandler;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\CarsBrandDTO;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\CarsBrandForm;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\CarsBrandHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[RoleSecurity('ROLE_CARS_BRAND_EDIT')]
final class EditController extends AbstractController
{
    #[Route('/admin/cars/brand/edit/{id}', name: 'admin.brand.newedit.edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity] CarsBrandEvent $CarsBrandEvent,
        CarsBrandHandler $CarsBrandHandler,
    ): Response
    {
        $CarsBrandDTO = new CarsBrandDTO();
        $CarsBrandEvent->getDto($CarsBrandDTO);

        // Форма
        $form = $this->createForm(CarsBrandForm::class, $CarsBrandDTO, [
            'action' => $this->generateUrl('reference-cars:admin.brand.newedit.edit', ['id' => $CarsBrandDTO->getEvent()]),
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('cars_brand'))
        {
            $this->refreshTokenForm($form);

            $handle = $CarsBrandHandler->handle($CarsBrandDTO);

            $this->addFlash
            (
                'admin.page.edit',
                $handle instanceof CarsBrand ? 'admin.success.edit' : 'admin.danger.edit',
                'admin.reference.cars.brand',
                $handle
            );

            return $this->redirectToReferer();

            //return $this->redirectToRoute('reference-cars:admin.brand.index');
        }

        return $this->render(['form' => $form->createView()]);
    }
}
