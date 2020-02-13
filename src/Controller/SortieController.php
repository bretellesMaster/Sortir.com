<?php

namespace App\Controller;


use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
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
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function sortieCreate(EntityManagerInterface $em, Request $request)
    {

        $sortie = new Sortie();
        $etat = new Etat();
        $lieu = new Lieu();
        $ville = new Ville();

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);


        $etat->setLibelle('Créée');

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
            $etat->setLibelle('Ouverte');


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
    public function sortieDetails(EntityManagerInterface $em)
    {
        $sortieRepository = $em->getRepository(Sortie::class);
        $sorties = $sortieRepository->findAll();
        return $this->render('sortie/sortieDetails.html.twig',
            ["sorties" => $sorties]);
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
    public function sortieCancel()
    {


        return $this->render('sortie/sortieCancel.html.twig');
    }
}
