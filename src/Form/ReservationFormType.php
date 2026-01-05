<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'choice_label' => 'roomNumber',
            ])
            ->add('checkInDate', DateType::class, ['widget' => 'single_text'])
            ->add('checkOutDate', DateType::class, ['widget' => 'single_text'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Reservation::class]);
    }
}
