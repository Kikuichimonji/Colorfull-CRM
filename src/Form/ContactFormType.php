<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('is_company', RadioType::class, [
                'constraints' => [
                    new IsTrue([
                        'message' => 'Veuillez choisir le status du contact (Société ou Particulier)'
                    ]),
                ],
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez entrer un nom de contact'
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom doit au moins contenir {{ limit }} charactères.',
                        'max' => 50,
                        'maxMessage' => 'Le nom ne doit pas contenir plus de {{ limit }} charactères.',
                    ]),
                ],
            ])
            ->add('phone1', TelType::class, [
                'constraints' => [
                    // new Regex([
                    //     'pattern' => "/(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/",
                    //     'message' => "Le numéro de téléphone n'est pas valide ",
                    // ])
                ],
            ])
            ->add('phone2', TelType::class, [
                'constraints' => [
                    // new Regex([
                    //     'pattern' => "/(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/",
                    //     'message' => "Le numéro de téléphone n'est pas valide ",
                    // ])
                ],
            ])
            ->add('email', EmailType::class,[
                "invalid_message" => "Ce format d'email n'est pas valide",
                'constraints' => [
                    new Email([
                        'message' => "Cet email n'est pas valide",
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
