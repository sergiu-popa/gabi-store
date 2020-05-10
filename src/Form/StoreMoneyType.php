<?php

namespace App\Form;

use App\Entity\Money;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoreMoneyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('amount', NumberType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Suma']
            ])
            ->add('notes', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Notițe']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Money::class,
        ]);
    }
}
