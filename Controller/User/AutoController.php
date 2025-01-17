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
use BaksDev\Products\Product\Repository\ProductAlternative\ProductAlternativeInterface;
use BaksDev\Reference\Cars\Forms\Filter\CarsFilterDTO;
use BaksDev\Reference\Cars\Forms\Filter\CarsFilterForm;
use BaksDev\Reference\Cars\Repository\Brands\CarBrandsChoice\CarBrandsChoiceRepository;
use BaksDev\Reference\Cars\Repository\Modification\CarsModificationDetail\CarsModificationDetailInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class AutoController extends AbstractController
{
    #[Route('/auto/{brand}/{model}/{modification}/{engine}/{power}', name: 'user.detail', methods: ['GET'])]
    public function detail(
        string $brand,
        string $model,
        string $modification,
        CarsModificationDetailInterface $carsModificationDetail,
        ProductAlternativeInterface $productAlternative,
        ?string $engine = null,
        ?string $power = null,
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

        $tiresField = isset($card['tire_field']) ? json_decode($card['tire_field']) : [];

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

            $tires[$tire->radius] = $alt;
        }

        return $this->render([
            'card' => $card,
            'tir' => $tires,
        ]);
    }

    #[Route('/auto', name: 'user.filter', methods: ['GET', 'POST'])]
    public function auto(
        Request $request,
        CarBrandsChoiceRepository $carBrandsChoice,
        CarsModificationDetailInterface $carsModificationDetail,
        ProductAlternativeInterface $productAlternative,
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
            $card = $carsModificationDetail->findCarDetail($filter->getBrand(), $filter->getModel(), $filter->getModification());

            $tiresField = json_decode($card['tire_field'], false, 512, JSON_THROW_ON_ERROR);

            $returnSeason = null;

            if('true' === $filter->getStuds())
            {
                $returnSeason[] = (object) ['field_uid' => '01876af0-ddfe-7a4b-a184-771635c4190d', 'field_value' => 'true'];
            }
            else
            {
                if($filter->getSeason())
                {
                    $returnSeason[] = (object) match ($filter->getSeason())
                    {
                        'summer', 'winter', 'all' => ['field_uid' => '01876af0-ddfe-7a4b-a184-771635481a8b', 'field_value' => $filter->getSeason()],
                        /* 'studs' => ['field_uid' => '01876af0-ddfe-7a4b-a184-771635c4190d', 'field_value' => 'true'], */
                        default => ['field_uid' => null, 'field_value' => null],
                    };
                }
            }


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
                    $returnSeason,
                );

                if(empty($alt))
                {
                    continue;
                }

                $tires[$tire->radius] = $alt;
            }
        }
        else
        {
            $brands = $carBrandsChoice->getDetailCollectionByTires();
        }

        return $this->render([
            'filter_cars' => $filterForm->createView(),
            'card' => $card,
            'tir' => $tires,
            'brands' => $brands,
        ]);
    }
}
