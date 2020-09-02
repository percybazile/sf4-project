<?php

namespace App\Form;

use App\Entity\Events;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class EventsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Email manquant.']),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom ne peut contenir plus de {{ limit }} caractères.'
                    ]),
                ]
            ])
            ->add('description', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Description manquante.']),
                    new Length([
                        'min' => 30,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères.',
                    ]), 
                ]
            ])
            ->add('lieu', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Lieu manquant.']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le pseudo doit contenir au moins {{ limit }} caractères.',
                    ]), 
                ]
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'Date manquante.']),
                ]
            ])
            
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Events::class,
        ]);
    }

}
