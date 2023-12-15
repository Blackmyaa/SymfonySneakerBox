<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Users;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder, 
        private SluggerInterface $slugger
        ){}

    public function load(ObjectManager $manager): void
    {

        $admin = new Users();
        $admin -> setEmail('admin@admin.fr');
        $admin -> setNom('Nom');
        $admin -> setPrenom('Prenom');
        $admin -> setAdresse('12 rue de l\'adresse');
        $admin -> setCodePostal('75002');
        $admin -> setVille('Paris');
        $admin -> setPassword(
            $this->passwordEncoder->hashPassword($admin, 'admin')
        );
        $admin -> setRoles(['ROLE_ADMIN']);
        
        $manager->persist($admin);



            
        //     $user = new Users();
        //     $user -> setEmail('user@user.fr');
        //     $user -> setNom('Nom');
        //     $user -> setPrenom('Prenom');
        //     $user -> setAdresse('12 rue de l\'adresse');
        //     $user -> setCodePostal('75002');
        //     $user -> setVille('Paris');
        //     $user -> setPassword(
        //         $this->passwordEncoder->hashPassword($user, 'user')
        //     );
        
        // $manager->persist($user);

        $faker = Faker\Factory::create('fr_FR');
        for($usr = 1; $usr <= 28; $usr++){

            $user = new Users();
            $user -> setEmail($faker->email);
            $user -> setNom($faker->lastName);
            $user -> setPrenom($faker->firstName);
            $user -> setAdresse($faker->address); // la Classe streetAddress ne fonctionne pas il faut utiliser la class address de phpFaker
            $user -> setCodePostal(str_replace(' ', '', $faker->postcode));
            $user -> setVille($faker->city);
            $user -> setPassword(
                $this->passwordEncoder->hashPassword($user, 'user')
            );
        
        $manager->persist($user);

        }


        $manager->flush();
    }
}
