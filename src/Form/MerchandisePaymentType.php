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
        // TODO show date only when adding? or preia de la zi?

        $builder
            ->add('provider', EntityType::class, [
                'class' => Provider::class,
                'query_builder' => function (ProviderRepository $r) { return $r->getQueryBuilder(); },
                'label' => false
            ])
            ->add('type', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Factură' => MerchandisePayment::TYPE_INVOICE,
                    'Bon' => MerchandisePayment::TYPE_BILL,
                ]
            ])
            ->add('amount', NumberType::class, [
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MerchandisePayment::class,
        ]);
    }
}
