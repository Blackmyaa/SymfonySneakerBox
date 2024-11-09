<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class AdresseEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresse', TextType::class,[
                'label'=>'Adresse',
                'required' => true,
                'attr'=> [
                    'class'=>'form-control mb-2', 
                    'placeholder' => 'Adresse',
                    ]
                ])

            ->add('codePostal', NumberType::class,[
                'label'=>'Code Postal',
                'required' => true,
                'attr'=> ['class'=>'form-control mb-2',
                'placeholder' => 'Code Postal'
                ]
            ])

            ->add('ville', TextType::class,[
                'label'=>'Ville',
                'required' => true,
                'attr'=> [
                    'class'=>'form-control mb-2',
                    'id' => 'localisation_ville', 
                    'placeholder' => 'Ville'
                    ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
