<?php

namespace App\Form;

use App\Entity\Categories;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class Flop5SalesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder 
            ->add('type', ChoiceType::class, [ 
                'choices' => [ 
                    'Général' => 'general', 
                    'Par catégorie' => 'category', 
                ], 
                'expanded' => true, 
                'multiple' => false, 
                'label' => 'Type de Flop 5',
                'attr'=> [
                    'class'=>'form-control mb-2', 
                    ] 
            ]) 
            
            ->add('category', EntityType::class, [ 
                'class' => Categories::class, 
                'choice_label' => 'nom', 
                'label' => 'Quelle Catégorie',
                'attr'=> [
                    'class'=>'form-control mb-2', 
                    ], 
                'required' => false,
                'placeholder'=>'-Sélectionnez une catégorie-',
                'group_by'=>'parent.nom', 
                'query_builder' => function (EntityRepository $er) { 
                    return $er->createQueryBuilder('c')
                    // On affiche uniquement les catégories qui contiennent des produits meme si le stock est à 0
                    ->innerJoin('c.produits', 'p') 
                    ->groupBy('c.id') 
                    ->having('COUNT(p.id) > 0') 
                    ->where('c.parent IS NOT NULL')
                    ->orderBy('c.nom', 'ASC'); 
                }, 
            ]) 
            
            ->add('start_date', DateType::class, [ 
                'widget' => 'single_text', 
                'label' => 'Date de début',
                'attr'=> [
                    'class'=>'form-control mb-2', 
                    ], 
            ]) 
            
            ->add('end_date', DateType::class, [ 
                'widget' => 'single_text', 
                'label' => 'Date de fin',
                'attr'=> [
                    'class'=>'form-control mb-2', 
                    ], 
            ]) 
            
            ->add('submit', SubmitType::class, [ 
                'label' => 'Rechercher', 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        
    }


}
