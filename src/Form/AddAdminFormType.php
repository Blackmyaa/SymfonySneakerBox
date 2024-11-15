<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AddAdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $options['currentUser']; 
        
        $rolesChoices = [ 
            'Administrateur Produits' => 'ROLE_PRODUCT_ADMIN' 
        ];

        if ($currentUser && $currentUser->hasRole('ROLE_ADMIN')) { 
            $rolesChoices['Administrateur'] = 'ROLE_ADMIN'; 
        }

        $builder
            ->add('nom', TextType::class, [ 
                'label' => 'Nom',
                'attr'=> ['class'=>'form-control mb-2'] 
                ])

            ->add('prenom', TextType::class, [ 
                'label' => 'Prénom',
                'attr'=> ['class'=>'form-control mb-2'] 
                ])

            ->add('email', EmailType::class, [
                'label'=>'Email',
                'attr'=> ['class'=>'form-control mb-2']
            ])

            ->add('password', PasswordType::class, [
                'label'=>'MDP',
                'attr'=> ['class'=>'form-control mb-2']
            ])
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
                    'placeholder' => 'Ville'
                    ]
                ])            
                
                ->add('roles', ChoiceType::class, [ 
                    'label' => 'Rôle', 
                    'choices' => $rolesChoices,
                    'multiple' => true, //Si on met true On verra des checkboxs
                    'expanded' => true, //Pour eviter l'affichage en mode menu déroulant et le probleme de conversion array to string
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
            'currentUser' => null,
        ]);
    }
}
