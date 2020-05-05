<?php

namespace App\Form;

use App\Entity\Merchandise;
use App\Entity\MerchandiseCategory;
use App\Entity\Provider;
use App\Repository\MerchandiseCategoryRepository;
use App\Repository\ProviderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
        $provider = $options['provider'];

        if (empty($provider)) {
            $builder->add('provider', EntityType::class, [
                'class' => Provider::class,
                'query_builder' => function (ProviderRepository $r) { return $r->getQueryBuilder(); }
            ]);
        } else {
            $merchandise = $options['data'];
            $merchandise->setProvider($this->providerRepository->find($provider));
        }

        $builder->add('category', EntityType::class, [
            'class' => MerchandiseCategory::class,
            'query_builder' => function (MerchandiseCategoryRepository $r) { return $r->getQueryBuilder(); }
        ])
            ->add('name')
            ->add('amount')
            ->add('enterPrice')
            ->add('exitPrice');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Merchandise::class,
            'provider' => null
        ]);
    }
}
