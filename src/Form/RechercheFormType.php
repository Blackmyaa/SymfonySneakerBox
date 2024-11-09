<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RechercheFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', TextType::class, [
                'label' => 'Nom du produit',
                'attr'=> ['class'=>'form-control mb-2', 'placeholder' => 'Nom'],
                'required' => false,
            ])

            ->add('description', TextType::class,[
                'label' => 'Entrez un mot clé',
                'attr'=> ['class'=>'form-control mb-2', 'placeholder' => 'Mot Clé'],
                'required' => false,
            ])

            ->add('minPrice', NumberType::class, [
                'label' => 'Prix minimum',
                'attr'=> ['class'=>'form-control mb-2', 'placeholder' => 'Prix mini'],
                'required' => false,
            ])

            ->add('maxPrice', NumberType::class, [
                'label' => 'Prix maximum',
                'attr'=> ['class'=>'form-control mb-2', 'placeholder' => 'Prix maxi'],
                'required' => false,
            ]);
    }
}
