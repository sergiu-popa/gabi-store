<?php

namespace App\Form;

use App\Entity\Merchandise;
use App\Repository\ProviderRepository;
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
            $builder->add('provider');
        } else {
            $merchandise = $options['data'];

            $merchandise->setProvider($this->providerRepository->find($provider));
        }

        $builder
            ->add('category')
            ->add('name')
            ->add('amount')
            ->add('enterPrice')
            ->add('exitPrice')
            ->add('date', DateType::class, [])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Merchandise::class,
            'provider' => null
        ]);
    }
}
