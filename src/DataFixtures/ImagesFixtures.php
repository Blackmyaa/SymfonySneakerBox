<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Images;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;
use Doctrine\Common\DataFixtures\DependentFixtureInterface
;

class ImagesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($img = 1; $img <= 200; $img++){
            $image = new Images();
            $image->setNom($faker->image(null, 640, 480));
            $product = $this->getReference('prod-'.rand(1,5));
            $image->setProduit($product);
            $manager->persist($image);

        }

        $manager->flush();
    }

    public function getDependencies(): array{
        return[
            ProduitsFixtures::class
        ];
    }
}
