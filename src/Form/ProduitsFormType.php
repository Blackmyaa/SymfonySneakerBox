<?php

namespace App\Form;

use App\Entity\Produits;
use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProduitsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextareaType::class, ['label' => 'Nom','attr'=> ['class'=>'form-control mb-2', 'placeholder' => 'Nom du produit']])
            ->add('description', TextareaType::class, ['label' => 'Description','attr'=> ['class'=>'form-control mb-2', 'placeholder' => 'Description']])
            ->add('prix', NumberType::class, ['label' => 'Prix en €','attr'=> ['class'=>'form-control mb-2', 'placeholder' => 'Prix']])
            ->add('stock', NumberType::class, ['label' => 'Quantité disponible','attr'=> ['class'=>'form-control mb-2', 'placeholder' => 'Stock']])
            ->add('categories', EntityType::class, [
                'label'=> 'Catégorie',
                'class' => Categories::class,
                'choice_label' => 'nom',
                'group_by'=>'parent.nom',
                'query_builder'=>function(CategoriesRepository $cr)
                {
                    return $cr->createQueryBuilder('c')
                        ->where('c.parent IS NOT NULL')
                        ->orderBy('c.nom', 'ASC');
                },
                'attr'=>['class'=>'form-control mb-2', 'placeholder' => 'Catégorie']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
