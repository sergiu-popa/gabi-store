<?php

namespace App\Form;

use App\Entity\Merchandise;
use App\Entity\MerchandiseCategory;
use App\Entity\Provider;
use App\Repository\MerchandiseCategoryRepository;
use App\Repository\ProviderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MerchandiseType extends AbstractType
{
    /** @var ProviderRepository */
    private $providerRepository;

    public function __construct(ProviderRepository $providerRepository)
    {
        $this->providerRepository = $providerRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Merchandise $merchandise */
        $merchandise = $options['data'];
        $provider = $options['provider'];

        if($merchandise->getId() === null) { // show or not provider only when adding new merchandise
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
            'provider' => null
        ]);
    }
}
