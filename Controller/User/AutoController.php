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
use BaksDev\Products\Product\Controller\Public\ModelController;
use BaksDev\Products\Product\Repository\Cards\ProductAlternative\ProductAlternativeInterface;
use BaksDev\Reference\Cars\Forms\Filter\CarsFilterDTO;
use BaksDev\Reference\Cars\Forms\Filter\CarsFilterForm;
use BaksDev\Reference\Cars\Repository\Brands\CarBrandsChoice\CarBrandsChoiceRepository;
use BaksDev\Reference\Cars\Repository\Modification\CarsModificationDetail\CarsModificationDetailInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class AutoController extends AbstractController
{
    #[Route('/auto/{brand}/{model}/{modification}/{engine}/{power}/{radius}/{width}/{profile}', name: 'user.detail', methods: ['GET'])]
    public function detail(
        string $brand,
        string $model,
        string $modification,
        CarsModificationDetailInterface $carsModificationDetail,
        ProductAlternativeInterface $productAlternative,
        ?string $engine = null,
        ?string $power = null,

        ?int $radius = null,
        ?int $width = null,
        ?int $profile = null

    ): Response
    {
        $card = $carsModificationDetail->findCarDetailByUrl(
            $brand,
            $model,
            $modification,
            $engine,
            $power,
        );

        if(!$card)
        {
            return new Response('Page Not Found', Response::HTTP_NOT_FOUND);
        }


        $carTires = isset($card['tire_field']) ? json_decode($card['tire_field']) : [];


        /**
         * Если радиус не определен - получаем из первого ключа
         */

        if(is_null($radius) && false === empty($carTires))
        {
            $radius = current($carTires)->radius;
        }

        $tiresField = [];

        /**
         * Если передан радиус - фильтруем по радиусу
         */

        if(false === is_null($radius) && false === empty($carTires))
        {
            $tiresField = array_filter($carTires, static function($tires) use ($radius) {
                return $tires->radius === $radius;
            });
        }

        /**
         * Если передана ширина - фильтруем по ширина
         */

        if(false === is_null($width) && false === empty($tiresField))
        {
            $tiresField = array_filter($tiresField, static function($tires) use ($width) {
                return $tires->width === $width;
            });
        }

        /**
         * Если передан профиль - фильтруем по профилю
         */

        if(false === is_null($profile) && false === empty($tiresField))
        {
            $tiresField = array_filter($tiresField, static function($tires) use ($profile) {
                return $tires->profile === $profile;
            });
        }


        /**
         * Определяем список рекомендованных шин для авто
         */

        $tires = [];

        foreach($tiresField as $tire)
        {
            if(empty($tire?->width) && empty($tire?->radius) && empty($tire?->profile))
            {
                break;
            }

            $alt = $productAlternative->fetchAllAlternativeAssociative(
                (string) $tire->radius,
                (string) $tire->width,
                (string) $tire->profile,
            );

            if(empty($alt))
            {
                continue;
            }

            $tires[] = $alt;
        }

        $engine ?: $engine = $card['modification_engine'] ?? null;
        $power ?: $power = $card['modification_power'] ?? null;

        return $this->render([
            'card' => $card,
            'tir' => $tires,
            'radius' => array_unique(array_column($carTires, 'radius')), // список рекомендованных радиусов
            'engine' => $engine,
            'power' => $power,
        ]);
    }

    #[Route('/auto', name: 'user.filter', methods: ['GET', 'POST'])]
    public function auto(
        Request $request,
        CarBrandsChoiceRepository $carBrandsChoice,
        CarsModificationDetailInterface $carsModificationDetail,
        ProductAlternativeInterface $productAlternative,
        HttpKernelInterface $httpKernel,
    ): Response
    {
        // Фильтр по авто
        $filter = new CarsFilterDTO();

        $filterForm = $this
            ->createForm(
                CarsFilterForm::class,
                $filter,
                ['action' => $this->generateUrl('reference-cars:user.filter')],
            )
            ->handleRequest($request);

        $brands = null;
        $card = null;
        $tires = [];

        if($filterForm->isSubmitted() && $filterForm->isValid() && $filterForm->has('cars_filter'))
        {
            $card = $carsModificationDetail
                ->findCarDetail($filter->getBrand(), $filter->getModel(), $filter->getModification());

            $carTires = isset($card['tire_field']) ? json_decode($card['tire_field'], false, 512, JSON_THROW_ON_ERROR) : [];
            $tire = current($carTires);

            $path['_controller'] = self::class.'::detail';
            $path['_route'] = 'reference-cars:user.detail';

            $path['brand'] = $card['brand_url'];
            $path['model'] = $card['model_url'];
            $path['modification'] = $card['modification_url'];
            $path['engine'] = $card['modification_engine'];
            $path['power'] = $card['modification_power'];

            $path['radius'] = $tire->radius;
            $path['_route_params'] = $path;

            $subRequest = $request->duplicate([], null, $path);

            return $httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }

        return $this->render([
            'filter_cars' => $filterForm->createView(),
            'card' => $card,
            'tir' => $tires,
            'brands' => $brands,
        ]);
    }
}
