<?php

namespace App\Form;

use App\Entity\Merchandise;
use App\Entity\MerchandiseCategory;
use App\Entity\Provider;
use App\Repository\MerchandiseCategoryRepository;
use App\Repository\ProviderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MerchandiseType extends AbstractType
{
    /** @var ProviderRepository */
    private $providerRepository;

    /** @var MerchandiseCategoryRepository */
    private $categoryRepository;

    public function __construct(
        ProviderRepository $providerRepository,
        MerchandiseCategoryRepository $categoryRepository
    ) {
        $this->providerRepository = $providerRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Merchandise $merchandise */
        $merchandise = $options['data'];
        $provider = $options['provider'];
        $category = $options['category'];

        // set category when creating merchandise if specified
        if ($merchandise->getId() === null && $category !== null) {
            $merchandise->setCategory($this->categoryRepository->find($category));
        }

        if ($merchandise->getId() === null) { // show or not provider only when adding new merchandise
            if (empty($provider)) {
                $builder->add('provider', EntityType::class, [
                    'class' => Provider::class,
                    'query_builder' => function (ProviderRepository $r) {
                        return $r->getQueryBuilder();
                    },
                    'attr' => ['class' => 'js-selectize', 'placeholder' => 'Selectează'],
                    'label' => false
                ]);
            } else {
                $merchandise->setProvider($this->providerRepository->find($provider));
            }

            $builder->add('vat', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    '9%' => 0.09,
                    '19%' => 0.19
                ],
                'expanded' => true
            ])
                ->add('paidWith', ChoiceType::class, [
                    'label' => 'Cum se plătește?',
                    'choices' => [
                        'Datorie' => Merchandise::PAID_WITH_DEBT,
                        'Factură' => Merchandise::PAID_WITH_INVOICE,
                        'Bon' => Merchandise::PAID_WITH_BILL,
                    ],
                    'expanded' => true
                ]);
        }

        $builder->add('category', EntityType::class, [
            'class' => MerchandiseCategory::class,
            'query_builder' => function (MerchandiseCategoryRepository $r) { return $r->getQueryBuilder(); },
            'attr' => ['class' => 'js-selectize', 'placeholder' => 'Selectează'],
            'label' => false
        ])
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Nume']
            ])
            ->add('amount', NumberType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Cantitate']
            ])
            ->add('enterPrice', NumberType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'P. Intrare']
            ])
            ->add('exitPrice', NumberType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'P. Iesire']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Merchandise::class,
            'provider' => null,
            'category' => null,
        ]);
    }
}
