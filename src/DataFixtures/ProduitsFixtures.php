<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Produits;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

;

class ProduitsFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger){}


    public function load(ObjectManager $manager): void
    {
        // use the factory to create a Faker\Generator instance
        $faker = Faker\Factory::create('fr_FR');

        for($prod = 1; $prod <= 150; $prod++){
            $product = new Produits();
            $product->setNom($faker->text(15));
            $product->setDescription($faker->text());
            $product->setSlug($this->slugger->slug($product->getNom())->lower());
            $product->setPrix($faker->numberBetween(900, 150000));
            $product->setStock($faker->numberBetween(0, 10));

            //On va chercher une référence de catégorie
            $category = $this->getReference('cat-'. rand(1, 9));
            $product->setCategories($category);

            $this->setReference('prod-'.$prod, $product);
            $manager->persist($product);
        }


        // $product = new Produits();
        // $product->setNom('produit1');
        // $product->setDescription('Dexcription produit 1');
        // $product->setSlug($this->slugger->slug($product->getNom())->lower());
        // $product->setPrix(25);
        // $product->setStock(8);

        // // On va chercher une référence de catégorie de maniere aléatoire
        // $categorie = $this->getReference('cat-'.rand(1,8));
        // $product->setCategories($categorie);
        
        // $this->setReference('prod-'.'1', $product);
        // $manager->persist($product);


        // $product = new Produits();
        // $product->setNom('produit2');
        // $product->setDescription('Dexcription produit 2');
        // $product->setSlug($this->slugger->slug($product->getNom())->lower());
        // $product->setPrix(28);
        // $product->setStock(13);
        // $categorie = $this->getReference('cat-'.rand(1,8));
        // $product->setCategories($categorie);
        
        // $this->setReference('prod-'.'2', $product);
        // $manager->persist($product);

        // $product = new Produits();
        // $product->setNom('produit3');
        // $product->setDescription('Dexcription produit 3');
        // $product->setSlug($this->slugger->slug($product->getNom())->lower());
        // $product->setPrix(28);
        // $product->setStock(13);
        // $categorie = $this->getReference('cat-'.rand(1,8));
        // $product->setCategories($categorie);
        
        // $this->setReference('prod-'.'3', $product);
        // $manager->persist($product);


        // $product = new Produits();
        // $product->setNom('produit4');
        // $product->setDescription('Dexcription produit 4');
        // $product->setSlug($this->slugger->slug($product->getNom())->lower());
        // $product->setPrix(28);
        // $product->setStock(13);
        // $categorie = $this->getReference('cat-'.rand(1,8));
        // $product->setCategories($categorie);
        
        // $this->setReference('prod-'.'4', $product);
        // $manager->persist($product);

        // $product = new Produits();
        // $product->setNom('produit5');
        // $product->setDescription('Dexcription produit 5');
        // $product->setSlug($this->slugger->slug($product->getNom())->lower());
        // $product->setPrix(28);
        // $product->setStock(13);
        // $categorie = $this->getReference('cat-'.rand(1,8));
        // $product->setCategories($categorie);
        
        // $this->setReference('prod-'.'5', $product);
        // $manager->persist($product);

        $manager->flush();
    }
}
