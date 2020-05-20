<?php

namespace App\Form;

use App\Entity\Day;
use App\Entity\Provider;
use App\Manager\ProviderManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DayEndType extends AbstractType
{
    /** @var ProviderManager */
    private $providerManager;

    public function __construct(ProviderManager $providerManager)
    {
        $this->providerManager = $providerManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Day $day */
        $day = $options['data'];

        $builder->add('bills_50_end', NumberType::class, [
            'label' => 'Bancnote de 50',
            'html5' => true
        ])
            ->add('bills_100_end', NumberType::class, [
                'label' => 'Bancnote de 100',
                'html5' => true
            ])
            ->add('z', NumberType::class, [
                'label' => 'Completează Z-ul'
            ]);

        $providersForToday = $this->providerManager->getForDay($day->getDate());

        if(\count($providersForToday) > 0) {
            $builder->add('orderProviders', EntityType::class, [
                'label' => 'Selectează furnizorii la care ai dat comandă astăzi',
                'class' => Provider::class,
                'choices' => $providersForToday,
                'multiple' => true,
                'expanded' => true,
                'required' => false
            ]);

            $builder->add('paidProviders', EntityType::class, [
                'label' => 'Selectează furnizorii pe care i-ai plătit astăzi',
                'class' => Provider::class,
                'choices' => $providersForToday,
                'multiple' => true,
                'expanded' => true,
                'required' => false
            ]);
        }

        $builder->add('submit', SubmitType::class, [
            'label' => 'Trimite',
            'attr' => [
                'class' => 'js-day-action btn btn-primary',
                'disabled' => true
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Day::class,
        ]);
    }
}
