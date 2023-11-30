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

namespace BaksDev\Reference\Cars\Controller\Admin\Model;


use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\CarsModelDTO;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\CarsModelForm;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\CarsModelHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[RoleSecurity('ROLE_CARS_MODEL_NEW')]
final class NewController extends AbstractController
{
    #[Route('/admin/cars/model/new', name: 'admin.model.newedit.new', methods: ['GET', 'POST'])]
    public function news(
        Request $request,
        CarsModelHandler $CarsModelHandler,
    ): Response
    {
        $CarsModelDTO = new CarsModelDTO();

        // Форма
        $form = $this->createForm(CarsModelForm::class, $CarsModelDTO, [
            'action' => $this->generateUrl('reference-cars:admin.model.newedit.new'),
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('cars_model'))
        {
            $handle = $CarsModelHandler->handle($CarsModelDTO);

            $this->addFlash
            (
                'admin.page.new',
                $handle instanceof CarsModel ? 'admin.success.new' : 'admin.danger.new',
                'admin.reference.cars.model',
                $handle
            );

            return $this->redirectToReferer();
            //return $this->redirectToRoute('reference-cars:admin.model.index', ['brand' => $handle->getBrand()->getUid()]);
        }

        return $this->render(['form' => $form->createView()]);
    }
}