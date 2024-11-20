<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder            
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'required' => true,
                'attr'=> [
                    'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$',
                    'class'=>'form-control mb-2', 'placeholder' => 'Entrez votre Em@il'
                    ]
            ])
            ->add('nom', TextType::class, ['label' => 'Nom','attr'=> ['class'=>'form-control mb-2', 'placeholder' => 'Entrez votre Nom']])
            ->add('prenom', TextType::class, ['label' => 'Prénom','attr'=> ['class'=>'form-control mb-2', 'placeholder' => 'Entrez votre Prénom']])
            ->add('adresse', TextType::class, ['label' => 'Votre Adresse','attr'=> ['class'=>'form-control mb-2', 'placeholder' => 'Entrez votre Adresse']])
            ->add('code_postal', TextType::class, ['label' => 'Code Postal','attr'=> ['class'=>'form-control mb-2', 'placeholder' => 'Ex: 59100']])
            ->add('ville', TextType::class, ['label' => 'Ville','attr'=> ['class'=>'form-control mb-2', 'placeholder' => 'Ex: Roubaix']])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],'label' => 'En m\'inscrivant j\'accepte l\'utilisation de mes données.',
            ])
            ->add('plainPassword', PasswordType::class, [
                                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password', 'class'=>'form-control mb-2', 'placeholder' => 'Entrez votre Mdp'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
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
