<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('type', TextType::class)
            ->add('description', TextType::class)
            ->add('startDate', DateTimeType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd h:m:s'
            ])
            ->add('endDate', DateTimeType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd h:m:s'
            ])
            ->add('state', TextType::class)
            ->add('city', TextType::class)
            ->add('address', TextType::class)
            ->add('coverImage', FileType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
