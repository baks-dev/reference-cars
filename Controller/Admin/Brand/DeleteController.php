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
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\Delete\CarsBrandDeleteDTO;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\Delete\CarsBrandDeleteForm;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\Delete\CarsBrandDeleteHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[RoleSecurity('ROLE_CARS_BRAND_DELETE')]
final class DeleteController extends AbstractController
{
    #[Route('/admin/cars/brand/delete/{id}', name: 'admin.brand.delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity] CarsBrandEvent $CarsBrandEvent,
        CarsBrandDeleteHandler $CarsBrandDeleteHandler,
    ): Response
    {

        $CarsBrandDeleteDTO = $CarsBrandEvent->getDto(CarsBrandDeleteDTO::class);
        $form = $this->createForm(CarsBrandDeleteForm::class, $CarsBrandDeleteDTO, [
            'action' => $this->generateUrl('reference-cars:admin.brand.delete', ['id' => $CarsBrandDeleteDTO->getEvent()]),
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('cars_brand_delete'))
        {
            $this->refreshTokenForm($form);

            $handle = $CarsBrandDeleteHandler->handle($CarsBrandDeleteDTO);

            $this->addFlash
            (
                'admin.page.delete',
                $handle instanceof CarsBrand ? 'admin.success.delete' : 'admin.danger.delete',
                'admin.reference.cars.brand',
                $handle
            );

            return $this->redirectToRoute('reference-cars:admin.brand.index');
        }

        return $this->render([
            'form' => $form->createView(),
            'name' => $CarsBrandEvent->getNameByLocale($this->getLocale()), // название согласно локали
        ]);
    }
}
