<?php

namespace App\Form;

use App\Entity\Expense;
use App\Entity\ExpenseCategory;
use App\Repository\ExpenseCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('category', EntityType::class, [
                'class' => ExpenseCategory::class,
                'query_builder' => function (ExpenseCategoryRepository $r) { return $r->getQueryBuilder(); }
            ])
            ->add('amount')
            ->add('notes');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Expense::class,
        ]);
    }
}
