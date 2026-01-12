<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Room;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'choice_label' => 'roomNumber',
                'attr' => ['class' => 'form__control']
            ])
            ->add('checkInDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form__control'],
                'required' => true
            ])
            ->add('checkOutDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form__control'],
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
