<?php

namespace App\Form;

use App\Entity\Provider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProviderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('days', ChoiceType::class, [
                'label' => 'în ce zile vine furnizorul la magazin?',
                'choices' => [
                    'Duminică' => 'Sunday',
                    'Luni' => 'Monday',
                    'Marți' => 'Tuesday',
                    'Miercuri' => 'Wednesday',
                    'Joi' => 'Thursday',
                    'Vineri' => 'Friday',
                ],
                'expanded' => true,
                'multiple' => true
            ])
            ->add('agent')
            ->add('mobileNumber')
            ->add('cui')
            ->add('town')
            ->add('phoneNumber');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Provider::class,
        ]);
    }
}
