<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    "attr" => [
                        "class" => "form-control"
                    ]
                ]
            )
            ->add(
                'Contents',
                TextareaType::class,
                [
                    "attr" => [
                        "class" => "form-control"
                    ]
                ]
            )
            ->add(
                'image',
                FileType::class,
                [
                    'label' => 'Image',


                    'mapped' => false,


                    'required' => false,

                    'attr'=>[
                        "class"=> "form-control"
                    ],

                    'constraints' => [
                        new File(
                            [
                                'maxSize' => '3024k',
                                'mimeTypes' => [
                                    'image/*',


                                ],
                                'mimeTypesMessage' => 'Please upload a valid image file',
                                'maxSizeMessage' => 'This file is too big'
                            ]
                        )
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Article::class,
            ]
        );
    }
}
