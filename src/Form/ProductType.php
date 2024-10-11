<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\subCategory;
use PHPUnit\TextUI\XmlConfiguration\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as ConstraintsFile;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('stock')
            ->add('image', FileType::class,[
                'label' => 'image de produit',
                'mapped' =>false,
                'required' =>false,
                'constraints'=>[
                    new ConstraintsFile([
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
            ->add('subCategories', EntityType::class, [
                'class' => subCategory::class,
                'choice_label' => 'name',
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
