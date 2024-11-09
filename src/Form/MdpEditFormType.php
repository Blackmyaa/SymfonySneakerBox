<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class MdpEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message' => 'Les mdp doivent correspondre',
                'label'=>'MDP',
                'required' => false,
                'first_options'  => ['label' => 'Nouveau MDR','attr'=> [
                    'class'=>'form-control mb-2', 
                    'placeholder' => 'MDP']],
                'second_options' => ['label' => 'Confirmez MDP','attr'=> [
                    'class'=>'form-control mb-2', 
                    'placeholder' => 'MDP']],
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
