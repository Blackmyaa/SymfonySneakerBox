<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BestSalesDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder 
            ->add('start_date', DateType::class, [ 
                'widget' => 'single_text', 
                'label' => 'Date de début',
                'attr'=> [
                    'class'=>'form-control mb-2', 
                    ] 
            ]) 
            
            ->add('end_date', DateType::class, [ 
                'widget' => 'single_text', 
                'label' => 'Date de fin',
                'attr'=> [
                    'class'=>'form-control mb-2', 
                    ] 
            ]) 
            
            ->add('submit', SubmitType::class, [ 
                'label' => 'Rechercher', 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
