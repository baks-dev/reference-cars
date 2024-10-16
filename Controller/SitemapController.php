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

namespace BaksDev\Reference\Cars\Controller;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Core\Type\UidType\ParamConverter;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Repository\AllCategoryByMenu\AllCategoryByMenuInterface;
use BaksDev\Reference\Cars\Repository\Brands\CarBrandByUrl\CarBrandByUrlInterface;
use BaksDev\Reference\Cars\Repository\Brands\CarBrandsChoice\CarBrandsChoiceRepository;
use BaksDev\Reference\Cars\Repository\Models\CarModelByUrl\CarModelByUrlInterface;
use BaksDev\Reference\Cars\Repository\Models\CarsModelsChoice\CarsModelsChoiceInterface;
use BaksDev\Reference\Cars\Repository\Modification\CarsModificationChoice\CarsModificationChoiceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;


#[AsController]
final class SitemapController extends AbstractController
{
    /**
     * Карта на разделы автомобилей
     */
    #[Route('/sitemaps/auto/sitemap.xml', name: 'sitemap', methods: ['GET'])]
    public function sitemap(CarBrandsChoiceRepository $carBrandsChoice): Response
    {
        $brands = $carBrandsChoice->getDetailCollectionByTires();

        $response = $this->render(['brands' => $brands]);
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }


    /**
     * Ссылки на бренды автомобилей
     */
    #[Route('/sitemap/auto/urls.xml', name: 'sitemap.brands.urls', methods: ['GET'])]
    public function urls(CarBrandsChoiceRepository $carBrandsChoice): Response
    {
        $brands = $carBrandsChoice->getDetailCollectionByTires();

        $response = $this->render(['urls' => $brands]);
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }


    /**
     * Карта моделей автомобилей
     */
    #[Route('/sitemaps/auto/models/sitemap.xml', name: 'sitemap.brands', methods: ['GET'])]
    public function sitemapBrands(CarBrandsChoiceRepository $carBrandsChoice): Response
    {

        $brands = $carBrandsChoice->getDetailCollectionByTires();

        $response = $this->render(['brands' => $brands]);
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }


    /**
     * Ссылки на модели
     */
    #[Route('/sitemap/auto/models/{brand}/urls.xml', name: 'sitemap.models.urls', methods: ['GET'])]
    public function sitemapModelsUrls(
        string $brand,
        CarBrandByUrlInterface $carBrandByUrl,
        CarsModelsChoiceInterface $carsModelsChoice,
    ): Response
    {
        $carModels = [];

        $CarBrand = $carBrandByUrl->getBrand($brand);

        if($CarBrand)
        {
            $carModels = $carsModelsChoice->getDetailModelsExistTires($CarBrand->getId());
        }

        $response = $this->render(
            [
                'brand' => $CarBrand,
                'models' => $carModels,
            ]
        );
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }


    /**
     * Карта на карта моделей автомобилей
     */
    #[Route('/sitemap/auto/models/{brand}/sitemap.xml', name: 'sitemap.models', methods: ['GET'])]
    public function sitemapModels(
        string $brand,
        CarBrandByUrlInterface $carBrandByUrl,
        CarsModelsChoiceInterface $carsModelsChoice,
    ): Response
    {
        $carModels = [];

        $CarBrand = $carBrandByUrl->getBrand($brand);

        if($CarBrand)
        {
            $carModels = $carsModelsChoice->getDetailModelsExistTires($CarBrand->getId());
        }

        $response = $this->render(
            [
                'brand' => $CarBrand,
                'models' => $carModels,
            ]
        );
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }


    /**
     * Ссылки на модификации
     */
    #[Route('/sitemap/auto/modification/{brand}/{model}/urls.xml', name: 'sitemap.modification.urls', methods: ['GET'])]
    public function sitemapModificationUrls(
        string $brand,
        string $model,
        CarModelByUrlInterface $carModelByUrl,
        CarsModificationChoiceInterface $carsModificationChoice,
    ): Response
    {
        $CarModel = $carModelByUrl->getModel($brand, $model);

        $carsModifications = $carsModificationChoice->getDetailCollectionByTires($CarModel->getModelId());

        $response = $this->render(
            [
                'card' => $CarModel,
                'mods' => $carsModifications,
            ]
        );
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }

}
