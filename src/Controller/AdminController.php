<?php

namespace App\Controller;


use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\VilleType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\AST\Functions\LengthFunction;
use App\Form\AdminSitesUpdateType;
use App\Form\SortieType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;


class AdminController extends AbstractController
{
    /////////////// Méthode liste lieu en administrateur ///////////////
    /**
     * @Route("/admin/adminListeLieux", name="adminListeLieux")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminListelieux(EntityManagerInterface $em)
    {
        $lieuRepo = $em->getRepository(Lieu::class);
        $lieux = $lieuRepo->findAll();

        return $this->render('admin/adminListeLieux.html.twig',
            ['lieux' => $lieux]);
    }

    ////////////// Méthode modifier lieu en administrateur //////////////

    /**
     * @Route("/admin/adminModifLieux/{id}", name="adminModifLieux")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminModifLieux(EntityManagerInterface $em, Request $request, $id)
    {
        $lieuRepo = $em->getRepository(Lieu::class);
        $lieux = $lieuRepo->find($id);

        $lieuForm = $this->createForm(LieuType::class, $lieux);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {

            $em->persist($lieux);
            $em->flush();

            $this->addFlash("success", "modification effectuée");

            return $this->redirectToRoute('adminListeLieux');
        }

        return $this->render('admin/adminModifLieux.html.twig', [
            'lieuForm' => $lieuForm->createView(),
        ]);
    }

    ///////////////// Méthode ajout lieu en administrateur /////////////////////

    /**
     * @Route("/admin/adminAjoutLieux", name="adminAjoutLieux")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminAjoutLieux(EntityManagerInterface $em, Request $request)
    {
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $em->persist($lieu);
            $em->flush();

            $this->addFlash("success", "ajout effectuée");

            return $this->redirectToRoute('adminListeLieux');
        }

        return $this->render('admin/adminAjoutLieux.html.twig', [
            'lieuForm' => $lieuForm->createView(),
        ]);
    }

    ///////////// Méthode supprimer lieu en administrateur ///////////////

    /**
     * @Route("/admin/adminDeleteLieux/{id}", name="adminDeleteLieux")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminDeleteLieux(EntityManagerInterface $em, $id)
    {
        $lieu = $em->getRepository(Lieu::class)->find($id);
        $lieu->setArchive(true);
        $em->persist($lieu);
        $em->flush();
        $this->addFlash("success", "lieu delete");
        return $this->redirectToRoute("adminListeLieux");
    }


    /** @Route("/adminSites", name="adminSites")
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminSites(EntityManagerInterface $em, Request $request)
    {
        $repsites = $em->getRepository(Site::class);
        $sites = $repsites->findAll();
        $repsites = $em->getRepository(Site::class);
        $sitesTrier = $repsites->findAll();

        return $this->render('admin/adminSites.html.twig', ["sites" => $sites,
            'sitesTriers' => $sitesTrier]);
    }

    /*
     * @IsGranted("ROLE_ADMIN")
     * @Route("/adminSites/Filtre", name="filtreSites")
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response

    public function filtre(EntityManagerInterface $em, Request $request)
    {
        $repsites = $em->getRepository(Site::class);
        $sites= $em->getRepository(Site::class)->findAll();

        $filtre = [
            'site' => $request->get('site'),
        ];

        $sitesTrier = $repsites->filtre($filtre);

        return $this->render('admin/adminSites.html.twig', [
            "sites"=>$sites,
            "sitesTriers"=>$sitesTrier,

        ]);

    }*/

    /**
     * @Route("/adminSites/Update/{id}",name="adminSitesUpdate")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminSitesUpdate(EntityManagerInterface $em, $id, Request $request)
    {

        $siteclass = new Site();
        $form = $this->createForm(AdminSitesUpdateType::class, $siteclass);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $siteRepository = $em->getRepository(Site::class);
            $site = $siteRepository->find($id);

            $nomSite = $form->get('nom')->getViewData();
            $site->setNom($nomSite);

            $em->persist($site);

            $em->flush();

            $this->addFlash("Success", "Votre site a bien été modifié !");

            return $this->RedirectToRoute("adminSites");
        }

        return $this->render('admin/siteUpdate.html.twig', [
            'siteForm' => $form->createView()
        ]);
    }


    /**
     * @Route("/adminSites/Delete/{id}",name="adminSitesDelete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminSitesDelete(EntityManagerInterface $em, $id)
    {

        $site = $em->getRepository(Site::class)->find($id);

        if (!$id) {
            return $this->RedirectToRoute("main");
        }

        $em->remove($site);
        $em->flush();

        $siteRepository = $em->getRepository(Site::class);
        $sites = $siteRepository->findAll();

        $this->addFlash("Success", "Le site a bien été supprimé !");

        return $this->render('admin/adminSites.html.twig', ["sites" => $sites]);
    }

    /**
     * @Route("/adminSortie", name="adminArchivageSortie")
     * @isGranted("ROLE_ADMIN")
     */
    public function archiveSortie(EntityManagerInterface $em)
    {

        $sorties = $em->getRepository(Sortie::class)->findAll();

        return $this->render('admin/adminSortie.html.twig', [
            'sorties' => $sorties,
        ]);

    }

    /**
     * @Route("/adminSites/Add/", name="adminSitesAdd")
     */
    public function adminSitesAdd(EntityManagerInterface $em, Request $request)
    {
        $site = new Site();
        $form = $this->createForm(AdminSitesUpdateType::class, $site);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $nomSite = $form->get('nom')->getViewData();
            $site->setNom($nomSite);

            $em->persist($site);

            $em->flush();

            $this->addFlash("Success", "Votre site a bien été modifié !");

            return $this->RedirectToRoute("adminSites");
        }

        return $this->render('admin/siteUpdate.html.twig', [
            'siteForm' => $form->createView()
        ]);
    }
}

