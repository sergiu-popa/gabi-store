<?php

namespace App\Form;

use App\Entity\MerchandisePayment;
use App\Entity\Provider;
use App\Repository\ProviderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MerchandisePaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('provider', EntityType::class, [
            'label' => false,
            'class' => Provider::class,
            'query_builder' => function (ProviderRepository $r) { return $r->getQueryBuilder(); },
            'attr' => ['class' => 'js-selectize', 'placeholder' => 'Selectează']
        ])
            ->add('paymentType', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Factură' => MerchandisePayment::TYPE_INVOICE,
                    'Bon' => MerchandisePayment::TYPE_BILL,
                ]
            ])
            ->add('amount', NumberType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Suma']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MerchandisePayment::class,
        ]);
    }
}
