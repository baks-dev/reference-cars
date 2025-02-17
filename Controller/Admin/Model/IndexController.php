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

declare(strict_types=1);

namespace BaksDev\Reference\Cars\Controller\Admin\Model;


use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Form\Search\SearchForm;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Core\Type\UidType\ParamConverter;
use BaksDev\Reference\Cars\Forms\Admin\CarsFilterDTO;
use BaksDev\Reference\Cars\Forms\Admin\CarsFilterForm;
use BaksDev\Reference\Cars\Repository\Models\AllCarsModel\AllCarsModelInterface;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[RoleSecurity('ROLE_CARS_MODEL')]
final class IndexController extends AbstractController
{
    #[Route('/admin/cars/models/{brand}/{page<\d+>}', name: 'admin.model.index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        AllCarsModelInterface $allCarsModel,
        #[ParamConverter(CarsBrandUid::class)] $brand,
        int $page = 0,
    ): Response
    {
        // Поиск
        $search = new SearchDTO();

        $searchForm = $this
            ->createForm(
                type: SearchForm::class,
                data: $search,
                options: ['action' => $this->generateUrl('reference-cars:admin.model.index')]
            )
            ->handleRequest($request);


        // Фильтр
        $filter = new CarsFilterDTO();
        $filter->setBrand($brand);
        $filterForm = $this->createForm(CarsFilterForm::class, $filter);
        $filterForm->handleRequest($request);

        if($filterForm->isSubmitted() && $filterForm->isValid())
        {
            if($filter->getModel())
            {
                return $this->redirectToRoute('reference-cars:admin.modification.index', ['brand' => $filter->getBrand(), 'model' => $filter->getModel()]);
            }

            if($filter->getBrand())
            {
                return $this->redirectToRoute('reference-cars:admin.model.index', ['brand' => $filter->getBrand()]);
            }
        }

        // Получаем список
        $CarsModel = $allCarsModel
            ->search($search)
            ->brand($brand)
            ->fetchAllCarsModelAssociative();

        return $this->render(
            [

                'query' => $CarsModel,
                'search' => $searchForm->createView(),
                'filter' => $filterForm->createView(),
            ]
        );
    }
}
