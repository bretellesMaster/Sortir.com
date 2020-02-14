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
     * @Route("/Sortie/Create/", name="sortieCreate")
     * @IsGranted("ROLE_USER")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function sortieCreate(EntityManagerInterface $em, Request $request)
    {

        $sortie = new Sortie();
        $lieu = new Lieu();
        $ville = new Ville();
        $etat = new Etat();

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $publication = $request->get('publication');

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

            if ($publication == 1) {
                $etat = $em->getRepository(Etat::class)->find(1);
                $sortie->setEtat($etat);
            } elseif ($publication == 2) {
                $etat = $em->getRepository(Etat::class)->find(2);
                $sortie->setEtat($etat);
            }

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
    public function sortieDetails(EntityManagerInterface $em, $id=null)
    {
        $sortieRepository = $em->getRepository(Sortie::class);
        if ($id !=null){
            $sorties = $sortieRepository->find($id);
            return $this->render('sortie/sortieDetails.html.twig',
            ["sorties" => $sorties]);
        }
        $sorties = $sortieRepository->findAll();
        return $this->redirectToRoute("main");
    }

    /**
     * @Route("/Sortie/Modif/{id}", name="sortieModif")
     * @IsGranted("ROLE_USER")
     */
    public function sortieModif(EntityManagerInterface $em, Request $request, $id)
    {
        $sortieRepository = $em->getRepository(Sortie::class);
        $sorties = $sortieRepository->find($id);

        $villeRepository = $em->getRepository(Ville::class);
        $villes = $villeRepository->find($id);

        $lieuRepository = $em->getRepository(Lieu::class);
        $lieux = $lieuRepository->find($id);

        return $this->render('sortie/sortieModif.html.twig',
            ["sorties"=> $sorties],
            ["villes"=> $villes],
            ["lieux"=> $lieux],
        );
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
