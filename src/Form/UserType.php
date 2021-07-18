<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',
                  TextType::class,
                  [
                      "attr" => [
                          "class" => "form-control"
                      ]
                  ])
            ->add('firstname',
                  TextType::class,
                  [
                      "attr" => [
                          "class" => "form-control"
                      ]
                  ])
            ->add('lastname',
                  TextType::class,
                  [
                      "attr" => [
                          "class" => "form-control"
                      ]
                  ])
            ->add('age',BirthdayType::class,[
                  "attr"=>[
                      "class" => "form-control"
                  ],
            ])
            ->add('email',
                  EmailType::class,
                  [
                      "attr" => [
                          "class" => "form-control"
                      ]
                  ])
            ->add('roles', ChoiceType::class,[
                "choices"=>[
                    'User' => "ROLE_USER",
                    'Administrator' => "ROLE_ADMIN",
                ],
                "multiple"=>true,
                "attr"=> [
                    "class" => "form-control"
                ]
            ])
            ->add('password')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
