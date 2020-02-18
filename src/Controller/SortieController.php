<?php

namespace App\Controller;


use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/Sortie/Create", name="sortieCreate")
     * @IsGranted("ROLE_USER")
     */
    public function sortieCreate(EntityManagerInterface $em, Request $request)
    {

        $sortie = new Sortie();

        $lieu = new Lieu();
        $ville = new Ville();


        $sortieForm = $this->createForm(SortieType::class, $sortie);


        $sortieForm->handleRequest($request);


        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $nomVille = $sortieForm->get('ville')->getViewData();
            $codePostal = $sortieForm->get('codePostal')->getViewData();
            $nomLieu = $sortieForm->get('nomLieu')->getViewData();
            $rueLieu = $sortieForm->get('rueLieu')->getViewData();
            $latitude = $sortieForm->get('latitude')->getViewData();
            $longitude = $sortieForm->get('longitude')->getViewData();


            $ville->setNom($nomVille);
            $ville->setCodePostal($codePostal);
            $lieu->setNom($nomLieu);
            $lieu->setLatitude($latitude);
            $lieu->setLongitude($longitude);
            $lieu->setRue($rueLieu);
            $lieu->setVille($ville);
            $sortie->setLieu($lieu);
            /*$etat->setId(6)
             * $etat->setLibellé("Annulé");
             * $sortie->setEtat($this);*/

            $sortie->setSite($this->getUser()->getSite());
            $sortie->setOrganisateur($this->getUser());

            $em->persist($ville);
            $em->persist($lieu);
            $em->persist($sortie);

            $em->flush();


            $this->addFlash('success', "Has been added !");
            return $this->redirectToRoute("main");
        }

        return $this->render('sortie/sortieCreate.html.twig',
            [
                "sortieForm" => $sortieForm->createView(),
            ]);
    }

    /**
     * @Route("/Sortie/Details/{id}", name="sortieDetails")
     * @IsGranted("ROLE_USER")
     */
    public function sortieDetails($id, EntityManagerInterface $em)
    {
        $userRepository = $em->getRepository(User::class);
        $users = $userRepository->findBy(['id' => $id]);
        $sortieRepository = $em->getRepository(Sortie::class);
        $sorties = $sortieRepository->findById(['id' => $id]);
        $lieuRepository = $em->getRepository(Lieu::class);
        $lieux = $lieuRepository->findById(['id' => $id]);
        $villeRepository = $em->getRepository(Ville::class);
        $villes = $villeRepository->findById(['id' => $id]);
        return $this->render('sortie/sortieDetails.html.twig',
            ["sorties" => $sorties,
                'users' => $users,
                'ville' => $villes,
                'lieu' => $lieux]);
    }

    /**
     * @Route("/Sortie/Modif/{id}", name="sortieModif")
     * @IsGranted("ROLE_USER")
     */
    public function sortieModif()
    {
        return $this->render('sortie/sortieModif.html.twig');
    }


    /**
     * @Route("/Sortie/Cancel{id}", name="sortieCancel")
     * @IsGranted("ROLE_USER")
     */
    public function sortieCancel($id,EntityManagerInterface $em)
    {


        return $this->render('sortie/sortieCancel.html.twig');
    }
}
