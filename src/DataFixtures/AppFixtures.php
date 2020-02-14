<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
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
        $faker = Faker\Factory::create('fr_FR');
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



            $ville = new Ville();
        $ville->setNom('Nantes');
        $ville->setCodePostal('44400');

        $manager->persist($ville);

        for($i = 0; $i < 20; $i++){
            $lieu = new Lieu();
            $lieu->setNom('Lieu'.($i+1));
            $lieu->setVille($ville);
            $lieu->setRue('Rue '.$faker->name);
            $lieu->setLongitude($faker->numberBetween(0, 40));
            $lieu->setLatitude($faker->numberBetween(0, 40));
            $manager->persist($lieu);



        }
        $etat = new Etat();
        $etat2 = new Etat();
        $etat3 = new Etat();
        $etat4 = new Etat();
        $etat5 = new Etat();
        $etat6 = new Etat();
        $etat->setLibelle('Créée');
        $etat2->setLibelle('Ouverte');
        $etat3->setLibelle('Clôturée');
        $etat4->setLibelle('Activité en cours');
        $etat5->setLibelle('Passée');
        $etat6->setLibelle('Annulé');
        $manager->persist($etat);
        $manager->persist($etat2);
        $manager->persist($etat3);
        $manager->persist($etat4);
        $manager->persist($etat5);
        $manager->persist($etat6);
$manager->flush();

        $lieux = $manager->getRepository(Lieu::class)->findAll();
        $orga = $manager->getRepository(User::class)->findAll();
        $sites = $manager->getRepository(Site::class)->findAll();
        $etats = $manager->getRepository(Etat::class)->findAll();
        for($i=0; $i < 50; $i++){
            $sortie = new Sortie();
            $sortie->setNom('Sortie'.($i+1));
            $sortie->setLieu($faker->randomElement($lieux));
            $sortie->setOrganisateur($faker->randomElement($orga));
            $sortie->setSite($faker->randomElement($sites));
            $datedebut = $faker->dateTimeBetween('now', '+4 months');
            $sortie->setEtat($faker->randomElement($etats));
            $sortie->setDateHeureDebut($datedebut);
            $datefin = $faker->dateTimeBetween('-10 days', $datedebut);
            $sortie->setDateLimiteInscription($datefin);
            $sortie->setDuree(4);
            $sortie->setInfosSortie($faker->sentence($nbWords = 6, $variableNbWords = true));
            $sortie->setNbInscriptionsMax(rand(5, 30));
            $manager->persist($sortie);





        }







        for($is = 0; $i < 30; $i++){
            $sortie = new Sortie();


        }


    $manager->flush();

    }
}
