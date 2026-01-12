<?php

namespace App\Form;

use App\Entity\Room;
use App\Entity\RoomType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType; // أضف هذا
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File; // أضف هذا للتحقق من الملف

class RoomFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roomNumber', TextType::class)
            ->add('roomType', EntityType::class, [
                'class' => RoomType::class,
                'choice_label' => 'type',
            ])
            // أضف حقل الصورة هنا
            ->add('image', FileType::class, [
                'label' => 'Room Photo (Image file)',
                'mapped' => false, // أخبر سيمفوني ألا يبحث عن حقل كائن في الـ Entity مباشرة
                'required' => false, // اختياري لكي لا يضطر الأدمن لرفع صورة عند كل تعديل
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG or WEBP)',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Room::class]);
    }
}
