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

namespace BaksDev\Reference\Cars\Forms\Filter;


use BaksDev\Field\Tire\Season\Form\TireSeasonFieldForm;
use BaksDev\Field\Tire\Studs\Form\TireStudsFieldForm;
use BaksDev\Reference\Cars\Repository\Brands\CarBrandsChoice\CarBrandsChoiceInterface;
use BaksDev\Reference\Cars\Repository\Models\CarsModelsChoice\CarsModelsChoiceInterface;
use BaksDev\Reference\Cars\Repository\Modification\CarsModificationChoice\CarsModificationChoiceInterface;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use BaksDev\Reference\Cars\Type\Modification\Characteris\CarsModificationCharacteristicsUid;
use BaksDev\Reference\Cars\Type\Modification\Id\CarsModificationUid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CarsFilterForm extends AbstractType
{

    private CarBrandsChoiceInterface $carBrandsChoice;
    private CarsModelsChoiceInterface $carsModelsChoice;
    private CarsModificationChoiceInterface $carsModificationChoice;

    public function __construct(
        CarBrandsChoiceInterface $carBrandsChoice,
        CarsModelsChoiceInterface $carsModelsChoice,
        CarsModificationChoiceInterface $carsModificationChoice
    )
    {

        $this->carBrandsChoice = $carBrandsChoice;
        $this->carsModelsChoice = $carsModelsChoice;
        $this->carsModificationChoice = $carsModificationChoice;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->add('brand', ChoiceType::class, [
            'choices' => $this->carBrandsChoice->getCollectionByTires(),
            'choice_value' => function(?CarsBrandUid $brand) {
                return $brand?->getValue();
            },
            'choice_label' => function(CarsBrandUid $brand) {
                return $brand->getAttr();
            },
            'label' => false
        ]);

        //        $builder->get('brand')->addModelTransformer(
        //            new CallbackTransformer(
        //                function($brand) {
        //                    return $brand instanceof CarsBrandUid ? $brand : $brand;
        //                },
        //                function($brand) {
        //                    return $brand ? new CarsBrandUid((string) $brand) : null;
        //                }
        //            )
        //        );


        //dd($this->carBrandsChoice->getCollection());

        //$brand = $this->carBrandsChoice->getCollection();
        //$models = $this->carsModelsChoice->getCollection(new CarsBrandUid('12249f10-ada4-732b-9cb9-af873f51c419'));

        //dd($models);

        //        $CarsBrandUid = new CarsBrandUid('12249f10-ada4-732b-9cb9-af873f51c419');
        //
        //        $builder->add('model', ChoiceType::class, [
        //            'choices' => $this->carsModelsChoice->getCollection($CarsBrandUid),
        //            'choice_value' => function(?CarsModelUid $model) {
        //                return $model?->getValue();
        //            },
        //            'choice_label' => function(CarsModelUid $model) {
        //                return $model->getAttr();
        //            },
        //            'label' => false
        //        ]);

        $builder->add('model', TextType::class, [
            //'choices' => [],
            'label' => false
        ]);


        $builder->get('model')->addModelTransformer(
            new CallbackTransformer(
                function($model) {
                    return $model instanceof CarsModelUid ? $model->getValue() : $model;
                },
                function($model) {
                    return $model ? new CarsModelUid((string) $model) : null;
                }
            )
        );

        $formModifierModel = function(FormInterface $form, ?CarsBrandUid $CarsBrandUid = null): void {
            //$positions = null === $sport ? [] : $sport->getAvailablePositions();

            if($CarsBrandUid)
            {
                $form->add('model', ChoiceType::class, [
                    'choices' => $this->carsModelsChoice->getCollectionByTires($CarsBrandUid),
                    'choice_value' => function(?CarsModelUid $model) {
                        return $model?->getValue();
                    },
                    'choice_label' => function(CarsModelUid $model) {
                        return $model->getAttr();
                    },
                    'choice_attr' => function(?CarsModelUid $model) {
                        return $model?->getOption() ? ['data-filter' => $model?->getOption()] : [];
                    },
                    'label' => false
                ]);

            }

            //            $form->add('model', ChoiceType::class, [
            //                'choices' => [],
            //                'label' => false
            //            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) use ($formModifierModel): void {
                /** @var CarsFilterDTO $data */
                $data = $event->getData();
                $formModifierModel($event->getForm(), $data->getBrand());
            }
        );


        $builder->get('brand')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) use ($formModifierModel): void {
                $brand = $event->getForm()->getData();
                $formModifierModel($event->getForm()->getParent(), $brand);
            }
        );

        $builder->add('modification', TextType::class, [
            //'choices' => [],
            'label' => false
        ]);


        $builder->get('modification')->addModelTransformer(
            new CallbackTransformer(
                function($modification) {
                    return $modification instanceof CarsModificationCharacteristicsUid ? $modification->getValue() : $modification;
                },
                function($modification) {
                    return $modification ? new CarsModificationCharacteristicsUid($modification) : null;
                }
            )
        );

        $formModifierModification = function(FormInterface $form, ?CarsModelUid $CarsModelUid = null): void {
            //$positions = null === $sport ? [] : $sport->getAvailablePositions();

            if($CarsModelUid)
            {
                $form->add('modification', ChoiceType::class, [
                    'choices' => $this->carsModificationChoice->getCollectionByTires($CarsModelUid),

                    'choice_value' => function(?CarsModificationCharacteristicsUid $mod) {
                        return $mod?->getValue();
                    },

                    'choice_label' => function(CarsModificationCharacteristicsUid $mod) {

                        $name = $mod->getAttr();

                        //                        if($mod->getOption())
                        //                        {
                        //                            $name .= ' - '.$mod->getOption();
                        //                        }

                        //                        if($mod->getCharacteristic())
                        //                        {
                        //                            $name .= ' ('.$mod->getProperty().'-'.$mod->getCharacteristic().' г.в.)';
                        //                        }
                        //                        else if($mod->getProperty())
                        //                        {
                        //                            $name .= ' ('.$mod->getProperty().' г.в. и выше)';
                        //                        }

                        return $name;
                    },

                    'choice_attr' => function(?CarsModificationCharacteristicsUid $mod) {

                        $name = null;

                        if($mod?->getOption())
                        {
                            $name .= $mod?->getOption();
                        }

                        if($mod?->getCharacteristic())
                        {
                            $name .= ' ('.$mod?->getProperty().'-'.$mod?->getCharacteristic().' г.в.)';
                        }
                        else if($mod?->getProperty())
                        {
                            $name .= ' ('.$mod?->getProperty().' г.в. и выше)';
                        }

                        return $name ? ['data-filter' => $name] : [];
                    },

                    'label' => false
                ]);
            }
        };


        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) use ($formModifierModification): void {
                /** @var CarsFilterDTO $data */
                $data = $event->getData();
                $formModifierModification($event->getForm(), $data->getModel());
            }
        );


        $builder->get('model')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) use ($formModifierModification): void {
                $model = $event->getForm()->getData();
                $formModifierModification($event->getForm()->getParent(), $model);
            }
        );


        $builder->add('season', TireSeasonFieldForm::class, [
            'required' => false,
        ]);


        $builder->add('studs', TireStudsFieldForm::class, [
            'required' => false,
        ]);


        /* Сохранить ******************************************************/
        $builder->add(
            'cars_filter',
            SubmitType::class,
            ['label' => 'Save', 'label_html' => true, 'attr' => ['class' => 'btn-primary']]
        );
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CarsFilterDTO::class,
            'method' => 'POST',
            'attr' => ['class' => 'w-100'],
        ]);
    }
}