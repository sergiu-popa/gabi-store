<?php

namespace App\Form;

use App\Entity\Merchandise;
use App\Entity\MerchandiseCategory;
use App\Entity\MerchandisePayment;
use App\Entity\Provider;
use App\Repository\MerchandiseCategoryRepository;
use App\Repository\MerchandiseRepository;
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

    /** @var MerchandiseRepository */
    private $repository;

    public function __construct(
        ProviderRepository $providerRepository,
        MerchandiseCategoryRepository $categoryRepository,
        MerchandiseRepository $repository
    ) {
        $this->providerRepository = $providerRepository;
        $this->categoryRepository = $categoryRepository;
        $this->repository = $repository;
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
                ])
                    ->add('isDebt', ChoiceType::class, [
                        'label' => 'Plătești?',
                        'choices' => [
                            'Da' => false, // if you pay, it's not debt
                            'Nu' => true
                        ],
                        'expanded' => true
                    ])
                    ->add('paymentType', ChoiceType::class, [
                        'label' => 'Cum plătești?',
                        'choices' => [
                            'Factură' => MerchandisePayment::TYPE_INVOICE,
                            'Bon' => MerchandisePayment::TYPE_BILL,
                        ],
                        'expanded' => true,
                        'attr' => ['class' => 'hidden']
                    ]);
            } else {
                $provider = $this->providerRepository->find($provider);
                $merchandise->setProvider($provider);

                $merchandises = $this->repository->findAll();
                $similarMerchandise = $this->repository->findSimilar($provider);
                if($similarMerchandise) {
                    $merchandise->setPaymentType($similarMerchandise->getPaymentType());
                    $merchandise->setIsDebt($similarMerchandise->isDebt());
                }
            }

            $builder->add('vat', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    '9%' => 9,
                    '19%' => 19
                ],
                'required' => false,
                'attr' => ['class' => 'js-merchandise-vat']
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
                'attr' => ['placeholder' => 'P. Intrare', 'class' => 'js-merchandise-enter-price']
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
