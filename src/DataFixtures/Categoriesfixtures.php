<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Categories;
use Symfony\Component\String\Slugger\SluggerInterface;
;

class Categoriesfixtures extends Fixture
{
    public $counter = 1;

    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        //premiere facon de creer une catégorie avec les DataFixtures
        // $parent1 = new Categories();
        // $parent1->setNom('Homme');
        // $parent1->setSlug($this->slugger->slug($parent1->getNom())->lower());
        // $manager->persist($parent1);

        $parent = $this->createCategorie('Femme', null, $manager);
        $this->createCategorie('Chaussures', $parent, $manager);
        $this->createCategorie('Bottines', $parent, $manager);

        $this->createCategorie('Accessoires', $parent, $manager);
        $this->createCategorie('Baskets', $parent, $manager);


        $parent = $this->createCategorie('Homme', null, $manager);
        $this->createCategorie('Chaussures', $parent, $manager);
        $this->createCategorie('Bottines', $parent, $manager);

        $this->createCategorie('Accessoires', $parent, $manager);


        // $parent2 = new Categories();
        // $parent2->setNom('Femme');
        // $parent2->setSlug($this->slugger->slug($parent2->getNom())->lower());
        // $manager->persist($parent2);

        // $categorie = new Categories();
        // $categorie->setNom('Chaussures');
        // $categorie->setSlug($this->slugger->slug($categorie->getNom())->lower());
        // $categorie->setParent($parent1);
        // $manager->persist($categorie);

        // $categorie1 = new Categories();
        // $categorie1->setNom('Chaussures');
        // $categorie1->setSlug($this->slugger->slug($categorie1->getNom())->lower());
        // $categorie1->setParent($parent2);
        // $manager->persist($categorie1);
        $manager->flush();
    }

    public function createCategorie(string $name, Categories $parent = null, ObjectManager $manager)
    {
        $categorie = new Categories();
        $categorie->setNom($name);
        $categorie->setSlug($this->slugger->slug($categorie->getNom())->lower());
        $categorie->setCategoryOrder(0);
        $categorie->setParent($parent);
        
        $manager->persist($categorie);

        $this->addReference('cat-'.$this->counter, $categorie);
        $this->counter++;

        return $categorie;
    }
}
