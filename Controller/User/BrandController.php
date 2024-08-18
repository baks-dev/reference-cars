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

namespace BaksDev\Reference\Cars\Controller\User;


use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Form\Search\SearchForm;
use BaksDev\Reference\Cars\Forms\Filter\CarsFilterDTO;
use BaksDev\Reference\Cars\Forms\Filter\CarsFilterForm;
use BaksDev\Reference\Cars\Repository\Brands\CarBrandByUrl\CarBrandByUrlInterface;
use BaksDev\Reference\Cars\Repository\Models\CarsModelsChoice\CarsModelsChoiceInterface;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BaksDev\Core\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class BrandController extends AbstractController
{
    #[Route('/auto/{brand}', name: 'user.brand', methods: ['GET', 'POST'])]
    public function index(
        //Request $request,
        string $brand,
        CarBrandByUrlInterface $carBrandByUrl,
        CarsModelsChoiceInterface $carsModelsChoice,
    ): Response
    {

        $CarBrand = $carBrandByUrl->getBrand($brand);

        if($CarBrand)
        {
            $carModels = $carsModelsChoice->getDetailModelsExistTires($CarBrand->getId());

           /* dump($carModels);*/
        }


        // Поиск
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
        $filter->setBrand($CarBrand->getId());
        $filterForm = $this->createForm(CarsFilterForm::class, $filter,
            ['action' => $this->generateUrl('reference-cars:user.filter')]);
        //$filterForm->handleRequest($request);

        return $this->render(
            [
                'brand' => $CarBrand,
                'models' => $carModels,
                'filter_cars' => $filterForm->createView(),
                //'search' => $searchForm->createView(),
            ]
        );
    }
}
