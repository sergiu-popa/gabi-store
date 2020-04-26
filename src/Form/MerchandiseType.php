<?php

namespace App\Form;

use App\Entity\Merchandise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MerchandiseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('provider')
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
        ]);
    }
}
