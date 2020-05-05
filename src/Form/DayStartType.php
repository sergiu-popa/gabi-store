<?php

namespace App\Form;

use App\Entity\Day;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DayStartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('bills_50_start', NumberType::class, [
                'label' => 'Bancnote de 50',
                'html5' => true
            ])
            ->add('bills_100_start', NumberType::class, [
                'label' => 'Bancnote de 100',
                'html5' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Începe ziua',
                'attr' => [
                    'class' => 'btn btn-primary'
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
