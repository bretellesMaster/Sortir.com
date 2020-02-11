<?php

namespace App\DataFixtures;

use App\Entity\Site;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    public function load(ObjectManager $manager)
    {
        $site = new Site();
        $site->setNom('Saint-Herblain');
        $manager->persist($site);

       $nathan = new User();
        $nathan->setSite($site);
        $nathan->setPseudo("Znatan");
        $nathan->setEmail("nathan.gonzalez2019@campus-eni.fr");
        $nathan->setNom("Gonzalez");
        $nathan->setPrenom('Nathan');

        $hash = $this->passwordEncoder->encodePassword($nathan, '1234');
        $nathan->setPassword($hash);
        $nathan->setRoles(["ROLE_ADMIN"]);
        $nathan->setTelephone("0102030405");

        $kevin = new User();
        $kevin->setSite($site);
        $kevin->setPseudo("bretello");
        $kevin->setEmail("kevin.levigouroux2019@campus-eni.fr");
        $kevin->setNom("Le Vigouroux");
        $kevin->setPrenom('Kevin');
        $hash = $this->passwordEncoder->encodePassword($kevin, '1234');
        $kevin->setPassword($hash);
        $kevin->setRoles(["ROLE_ADMIN"]);
        $kevin->setTelephone("0102030405");


       $bunny = new User();
        $bunny->setSite($site);
        $bunny->setPseudo("bbunny");
        $bunny->setEmail("bunny.sin2019@campus-eni.fr");
        $bunny->setNom("Sin");
        $bunny->setPrenom('Bunny');
        $hash = $this->passwordEncoder->encodePassword($bunny, '1234');
        $bunny->setPassword($hash);
        $bunny->setRoles(["ROLE_ADMIN"]);
        $bunny->setTelephone("0102030405");



       $damien = new User();
        $damien->setSite($site);
        $damien->setPseudo("Mimi");
        $damien->setEmail("damien.nicolleau2019@campus-eni.fr");
        $damien->setNom("Nicolleau");
        $damien->setPrenom('Damien');
        $hash = $this->passwordEncoder->encodePassword($damien, '1234');
        $damien->setPassword($hash);
        $damien->setRoles(["ROLE_ADMIN"]);
        $damien->setTelephone("0102030405");


        $manager->persist($nathan);
        $manager->persist($kevin);
        $manager->persist($bunny);
        $manager->persist($damien);



        for($i=0; $i < 10; $i++){
            $faker = Faker\Factory::create('es_ES');
            $user = new User();
            $user->setSite($site);
            $user->setPseudo($faker->userName);
            $user->setPrenom($faker->firstName);
            $user->setNom($faker->lastName);
            $user->setEmail($user->getPrenom().".".$user->getNom().$faker->year('now')."@campus-eni.fr");
            $user->setRoles(["ROLE_USER"]);
            $user->setTelephone('0102030405');
            $hash = $this->passwordEncoder->encodePassword($user, '1234');
            $user->setPassword($hash);
            $manager->persist($user);


        }
    $manager->flush();

    }
}
