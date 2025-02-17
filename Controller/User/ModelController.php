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

namespace BaksDev\Reference\Cars\Controller\User;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Reference\Cars\Forms\Filter\CarsFilterDTO;
use BaksDev\Reference\Cars\Forms\Filter\CarsFilterForm;
use BaksDev\Reference\Cars\Repository\Models\CarModelByUrl\CarModelByUrlInterface;
use BaksDev\Reference\Cars\Repository\Modification\CarsModificationChoice\CarsModificationChoiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ModelController extends AbstractController
{
    #[Route('/auto/{brand}/{model}', name: 'user.model', methods: ['GET', 'POST'])]
    public function index(
        string $brand,
        string $model,
        CarModelByUrlInterface $carModelByUrl,
        CarsModificationChoiceInterface $carsModificationChoice,
    ): Response
    {
        $CarModel = $carModelByUrl->getModel($brand, $model);

        $carsModifications = $carsModificationChoice->getDetailCollectionByTires($CarModel->getModelId());


        //        // Поиск
        //        $search = new SearchDTO();
        //        $searchForm = $this->createForm(SearchForm::class, $search);
        //        $searchForm->handleRequest($request);


        // Фильтр
        // $filter = new ProductsStocksFilterDTO($request, $ROLE_ADMIN ? null : $this->getProfileUid());
        // $filterForm = $this->createForm(ProductsStocksFilterForm::class, $filter);
        // $filterForm->handleRequest($request);

        // Получаем список
        //$wqwqwqwq = $allwqwqwqwq->fetchAllwqwqwqwqAssociative($search);


        // Фильтр по авто
        $filter = new CarsFilterDTO();
        $filter->setBrand($CarModel->getBrandId());
        $filter->setModel($CarModel->getModelId());
        $filterForm = $this->createForm(
            CarsFilterForm::class,
            $filter,
            ['action' => $this->generateUrl('reference-cars:user.filter')],
        );
        //$filterForm->handleRequest($request);

        return $this->render(
            [
                'card' => $CarModel,
                'mods' => $carsModifications,
                'filter_cars' => $filterForm->createView(),
                //'search' => $searchForm->createView(),
            ],
        );
    }
}
