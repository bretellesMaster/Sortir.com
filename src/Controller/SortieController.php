<?php

namespace App\Controller;


use App\Entity\Lieu;
use App\Entity\Site;
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


    /**
     * @IsGranted("ROLE_USER")
     * @Route("/main2", name="filtre")
     * @param EntityManagerInterface $em
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function filtre(EntityManagerInterface $em, Request $request){
       $rep = $em->getRepository(Sortie::class);
       $sites = $em->getRepository(Site::class)->findAll();

        $filtre = [
            'search' => $request->get('search'),
            'site' => $request->get('site'),
            'dateDebut' => $request->get('dateDebut'),
            'dateFin' => $request->get('dateFin'),
            'checkbox1' => $request->get('checkbox1'),
            'checkbox2' => $request->get('checkbox2'),
            'checkbox3' => $request->get('checkbox3'),
            'checkbox4' => $request->get('checkbox4'),


        ];
        $user = $this->getUser();

        $sorties = $rep->filtre($filtre, $user);

        return $this->render('main/index.html.twig', [
            'sorties' => $sorties,
            'sites'=>$sites

            ]);
    }

}
