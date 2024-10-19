<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\SubCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File as ConstraintsFile;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('image', FileType::class,[
                'label' => 'image de produit',
                'mapped' =>false,
                'required' =>false,
                'constraints'=>[
                    new ConstraintsFile ([
                        "maxSize"=>"1024",
                        "mimeTypes"=>[
                            'image/jpg',
                            'image/png',
                            'image/jpeg'
                        ],
                        'maxSizeMessage' => 'Votre image ne doit pas dépasser les 1024ko',
                        'mimeTypesMessage'=>'Votre image de produit doit être au format valide(png, jpg, jpeg)'
                    ])
                ]
            ])
            // ->add('stock')
            ->add('subCategories', EntityType::class, [
                'class' => SubCategory::class,
                'choice_label' => 'id',
                'multiple' => true,
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