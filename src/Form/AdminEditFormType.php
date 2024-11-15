<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AdminEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $options['currentUser']; 
        
        $rolesChoices = [ 
            'Administrateur Produits' => 'ROLE_PRODUCT_ADMIN',
        ];

        if ($currentUser && $currentUser->hasRole('ROLE_ADMIN')) { 
            $rolesChoices['Administrateur'] = 'ROLE_ADMIN'; 
        }
        
        $builder
        ->add('roles', ChoiceType::class, [ 
            'label' => 'Nouveau Rôle',
            'choices' => $rolesChoices,
            'multiple' => true, //Si on met true On verra des checkboxs
            'expanded' => true, //Pour eviter l'affichage en mode menu déroulant et le probleme de conversion array to string
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
            'currentUser' => null,
        ]);
    }
}
