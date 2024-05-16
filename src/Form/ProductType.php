<?php

namespace App\Form;

use App\Entity\File;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Taxes;
use Symfony\Component\Form\Extension\Core\Type\FileType as SymfonyFileType;
use Symfony\Component\Validator\Constraints\File as ConstraintFile;


class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('marque')
            ->add('priceHT')
            ->add('category',
                EntityType::class, [
                    'class' => Category::class,
                    'choice_label' => 'name',
                ]
            )
            ->add('taxes', EntityType::class, [
                'class' => Taxes::class,
                'choice_label' => 'name',
            ])
            ->add('Stock')
            ->add('fileName', SymfonyFileType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new ConstraintFile([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ]
                    ])
                ],
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
