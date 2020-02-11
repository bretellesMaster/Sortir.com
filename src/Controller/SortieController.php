<?php

namespace App\Controller;


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
     */
    public function sortieCreate(EntityManagerInterface $em, Request $request)
    {

        $sortie= new Sortie();

        $lieu = new Lieu();


        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $sortieForm=$this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);


        if($sortieForm->isSubmitted() && $sortieForm->isValid()){

            $lieu->setNom();
            $lieu->setRue();
            $lieu->getLongitude();
            $lieu->setLatitude();
            $em->persist($lieu);
            $sortie->setNom();
            $sortie->setSite();
            $sortie->setInfosSortie();
            $sortie->setNbInscriptionsMax();
            $sortie->setDuree();
            $sortie->setDateLimiteInscription();
            $sortie->setDateHeureDebut();
            $em->persist($sortie);

            $em->flush();


            $this->addFlash('success',"Has been added !");
            return $this->redirectToRoute("main");
        }

        return $this->render('sortie/sortieCreate.html.twig',
            [
                "sortieForm"=>$sortieForm->createView(),
                "lieuForm"=>$lieuForm->createView(),


            ]);
    }

    /**
     * @Route("/Sortie/Details/{id}", name="sortieDetails")
     * @IsGranted("ROLE_USER")
     */
    public function sortieDetails(EntityManagerInterface $em)
    {
        $sortieRepository=$em->getRepository(Sortie::class);
        $sorties=$sortieRepository->findAll();
        return $this->render('sortie/sortieDetails.html.twig',
            ["sorties"=>$sorties]);
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
