<?php

namespace App\Controller;


use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\SortieCancelType;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    //////////////// Méthode créer sortie ///////////////////
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

    //////////////// Méthode affichage détail de la sortie ////////////////
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
            ['sorties' => $sorties,
                'users' => $users,
                'ville' => $villes,
                'lieu' => $lieux]);
    }

    //////////////// Méthode formulaire modifier une sortie /////////////////
    /**
     * @Route("/Sortie/Modif/{id}", name="sortieModif")
     * @IsGranted("ROLE_USER")
     */
    public function sortieModif(EntityManagerInterface $em, Request $request, $id)
    {

        $repo = $em->getRepository(Sortie::class);
        $sortie = $repo->find($id);

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $em->flush();

            $this->addFlash("success", "modification effectuée");
            return $this->redirectToRoute('main', ['id' => $id]);
        }

        return $this->render('sortie/sortieModif.html.twig',[
            'sortieForm'=> $form->createView(),
            'sortie'=> $sortie,
        ]);
    }

    ///////////////// Méthode afficher le détail d'une sortie à annuler /////////////////
    /**
     * @Route("/Sortie/Cancel/{id}", name="sortieDetailCancel")
     * @IsGranted("ROLE_USER")
     */
    public function sortieDetailCancel(EntityManagerInterface $em, Request $request, $id)
    {
        $sortieRepository = $em->getRepository(Sortie::class);
        $sorties = $sortieRepository->findBy(["id" => $id]);

        return $this->render('sortie/sortieCancel.html.twig',
            ["sorties" => $sorties]);

    }

    //////////////// Méthode pour passer l'état de la sortie a "annulé" ////////////////////
    /**
     * @Route("/Sortie/Cancel2/{id}", name="sortieCancel")
     * @IsGranted("ROLE_USER")
     */
    public function sortieCancel(EntityManagerInterface $em, Request $request, $id)
    {
        $sortie = $em->getRepository(Sortie::class)->find($id);
        $etat = $em->getRepository(Etat::class)->find(6);
        $sortie->setEtat($etat);

        $em->persist($sortie);
        $em->flush();

        $this->addFlash('success', "Sortie cancel !");

        return $this->redirectToRoute("main");
    }

    ////////////// Méthode filtre page principale ////////////////////
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/main2", name="filtre")
     * @param EntityManagerInterface $em
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public
    function filtre(EntityManagerInterface $em, Request $request)
    {
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
            'sites' => $sites

        ]);

    }

}

