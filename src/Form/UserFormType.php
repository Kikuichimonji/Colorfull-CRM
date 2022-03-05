<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Image;

class UserFormType extends AbstractType
{
    /**
     * Validate the user profile form
     * @param FormBuilderInterface $builder    
     * @param array $options    
     **/
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'constraints' => [
                    // new Length([
                    //     'min' => 8,
                    //     'minMessage' => 'Votre mot de passe doit au moins contenir {{ limit }} charactères',
                    //     'max' => 30,
                    //     'maxMessage' => 'Votre mot de passe ne doit pas contenir plus de {{ limit }} charactères',
                    // ]),
                    new Regex([
                        'pattern' => "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[_@$!%*?&])[A-Za-z\d_@$!%*?&]{8,30}$/",
                        'message' => 'Le mot de passe doit contenir entre 8 et 30 charactères dont 1 majuscule, 1 minuscule, 1 chiffre, et 1 charactère spécial ( @$!%*?&_ ).',
                    ])
                ],
            ])
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre prénom doit au moins contenir {{ limit }} charactères.',
                        'max' => 70,
                        'maxMessage' => 'Votre prénom ne doit pas contenir plus de {{ limit }} charactères.',
                    ]),
                    new NotNull([
                        'message' => 'Le prénom ne peut pas être vide.'
                    ])
                ],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit au moins contenir {{ limit }} charactères.',
                        'max' => 70,
                        'maxMessage' => 'Votre nom ne doit pas contenir plus de {{ limit }} charactères.',
                    ]),
                    new NotNull([
                        'message' => 'Le nom ne peut pas être vide.'
                    ])
                ],
            ])
            ->add('phone', TelType::class, [
                'constraints' => [
                    new Regex([
                        'pattern' => "/(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/",
                        'message' => "Le numéro de téléphone n'est pas valide ",
                    ])
                ],
            ])
            ->add('email', TextType::class, [
                'constraints' => [
                    new Regex([
                        'pattern' => "/^[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{1,4}$/",
                        'message' => "L'email n'est pas valide ",
                    ])
                ],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "allow_extra_fields" => true
        ]);
    }
}
