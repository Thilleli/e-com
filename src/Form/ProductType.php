<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Taxes;
use App\Entity\Images;


class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('priceHT')
            ->add('category',
                EntityType::class, [
                    'class' => Category::class,
                    'choice_label' => 'namename',
                ]
            )
            ->add('taxes', EntityType::class, [
                'class' => Taxes::class,
                'choice_label' => 'name',
            ])
            ->add('Stock')
            ->add('image',EntityType::class, [
                'class' => Images::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
