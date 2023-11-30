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

namespace BaksDev\Reference\Cars\Controller\Admin\Modification;


use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Reference\Cars\Entity\Modification\CarsModification;
use BaksDev\Reference\Cars\Entity\Modification\Event\CarsModificationEvent;
use BaksDev\Reference\Cars\UseCase\Modification\Delete\CarsModificationDeleteHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[RoleSecurity('ROLE_CARS_MODIFICATION_DELETE')]
final class DeleteController extends AbstractController
{
    #[Route('/admin/cars/modification/delete/{id}', name: 'admin.modification.delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity] CarsModificationEvent $CarsModificationEvent,
        CarsModificationDeleteHandler $CarsModificationDeleteHandler,
    ): Response
    {


        dd(544546);

        $CarsModificationDeleteDTO = new CarsModificationDeleteDTO();
        $CarsModificationEvent->getDto($CarsModificationDeleteDTO);
        $form = $this->createForm(CarsModificationDeleteForm::class, $CarsModificationDeleteDTO, [
            'action' => $this->generateUrl('reference-cars:admin.delete', ['id' => $CarsModificationDeleteDTO->getEvent()]),
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('cars_modification_delete'))
        {
            $handle = $CarsModificationDeleteHandler->handle($CarsModificationDeleteDTO);

            $this->addFlash
            (
                'admin.page.delete',
                $handle instanceof CarsModification ? 'admin.success.delete' : 'admin.danger.delete',
                'admin.reference.cars.modification',
                $handle
            );

            return $this->redirectToReferer();


            //return $this->redirectToRoute('CarsModification:admin.index');
        }

        return $this->render([
            'form' => $form->createView(),
            'name' => $CarsModificationEvent->getNameByLocale($this->getLocale()), // название согласно локали
        ]);
    }
}
