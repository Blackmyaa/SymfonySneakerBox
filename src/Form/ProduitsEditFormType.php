<?php

namespace App\Form;

use App\Entity\Produits;
use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProduitsEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextareaType::class, ['label' => 'Nom','attr'=> ['class'=>'form-control mb-2', 'placeholder' => 'Nom du produit']])
            ->add('description', TextareaType::class, ['label' => 'Description','attr'=> ['class'=>'form-control mb-2', 'placeholder' => 'Description']])
            ->add('prix', MoneyType::class, ['label' => false,'attr'=> ['class'=>'form-control mb-2', 'placeholder' => 'Prix']])

            //Le moneyType fait que dans le formulaire le € va apparaitre dans le label
            
            // Autre manière d'appliquer des contraintes au prix qui doit obligatoirement etre positif + passage en parametre d'un message d'erreur.

            // ->add('price', MoneyType::class, options:[
            //     'label' => 'Prix',
            //     'divisor' => 100, <- cette ligne permettra de retirer les x100 et /100 dans le AdminProductController qui permettait d'afficher des prix ronds en BDD
            //     'constraints' => [
            //         new Positive(
            //             message: 'Le prix ne peut être négatif'
            //         )
            //     ]
            // ])
            
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
            ->add('images', FileType::class, [
                'label'=> false,
                'multiple'=> true,
                'mapped'=> false,
                'required'=>false,
                'attr' => [
                    'data-max-files' => 10 // On ajoute cette option pour le contrôle JavaScript (si besoin)
                ],
                // 'constraints'=> [
                //     new All( // On rajoute le new All pour pouvoir ajouter plusieurs images simultanément car le new Image ne permet d'en ajouter qu'une à la fois.
                //         new Image([
                //             'maxWidth'=>1920,
                //             'maxWidthMessage'=>'L\'image doit faire moins de {{ max_width }} pixels de largeur',
                //         ])
                //     )
                // ]
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
