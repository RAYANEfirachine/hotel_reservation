<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\User;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, ['required' => false])
            ->add('lastName', TextType::class, ['required' => false])
            ->add('email', EmailType::class)
            ->add('plainPassword', PasswordType::class, ['mapped' => false])
            ->add('phone', TextType::class, ['required' => false])
            ->add('birthDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('identityType', ChoiceType::class, [
                    'choices'  => [
                        'Identity Card (CIN)' => 'cin',
                        'Passport'            => 'passport',
                    ],
                    'expanded' => false, // false = dropdown select, true = radio buttons
                    'multiple' => false,
                    'placeholder' => 'Select here',
                    'attr' => ['class' => 'form-select'], // Bootstrap style
                ])
            ->add('identityNumber', TextType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
