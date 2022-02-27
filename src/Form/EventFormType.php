<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Ce message est trop court, 3 charactères minimum',
                        'max' => 50,
                        'maxMessage' => "Ce message est trop long, 50 charactères maximum"
                    ])
                ]
            ])
            ->add('description')
            ->add('color')
            ->add('isImportant')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "allow_extra_fields" => true
        ]);
    }
}
