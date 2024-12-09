<?php 

// src/Form/ContactType.php
namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type; 
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;


class ContactFormType extends AbstractType
{
    private $security; 
    public function __construct(Security $security) { 
        $this->security = $security; 
    }

    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $user = $this->security->getUser();
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'data' => $user ? $user->getNom() : null,
                'attr' => [ 'readonly' => $user ? 'readonly' : false, ],
                ])

            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'data' => $user ? $user->getPrenom() : null,
                'attr' => [ 'readonly' => $user ? 'readonly' : false, ],
                ])

            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required'=>true,
                'attr'=> [
                    'readonly' => $user ? 'readonly' : false,
                    'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$'
                ],
                'data' => $user ? $user->getEmail() : null,
                ])

            ->add('sujet', ChoiceType::class, [
                'label' => 'Votre message porte sur',
                'placeholder'=>'Choisissez un sujet',
                'choices' => [
                    'Un produit' => 'produit',
                    'Une commande' => 'commande',
                    'Une expédition' => 'expedition',
                    'Un autre sujet'=>'autre',
                    
                ], 
            ])

            ->add('message', TextareaType::class, [
                'label' => 'Message'
            ])
            
            ->add('captcha', Recaptcha3Type::class, [ 
                'constraints' => new Recaptcha3(), 
                'action_name' => 'homepage', 
                'locale' => 'fr', 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}