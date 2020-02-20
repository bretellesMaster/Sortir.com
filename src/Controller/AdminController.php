<?php

namespace App\Controller;


use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\AdminSitesUpdateType;
use App\Form\FiltreSiteType;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AdminController extends AbstractController
{
    /**
     * @Route("/adminLieux", name="adminLieux")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminLieux()
    {
        return $this->render('admin/adminLieux.html.twig');
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/adminSites", name="adminSites")
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminSites(EntityManagerInterface $em)
    {
        $filtreSiteForm=$this->createFormBuilder(FiltreSiteType::class);
        $repsites = $em->getRepository(Site::class);
        $sites = $repsites->findAll();
        return $this->render('admin/adminSites.html.twig', ["sites" => $sites]);
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
     * @param EntityManagerInterface $em
     * @param $id
     * @param Request $request
     * @Route("/adminSites/Update/{id}",name="adminSitesUpdate")
     * @IsGranted("ROLE_ADMIN")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
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
            'siteForm' => $form->createView()]);
    }


    /**
     * @param $id
     * @Route("/adminSites/Delete/{id}",name="adminSitesDelete")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function adminSitesDelete(EntityManagerInterface $em,$id)
    {

            $site = $em->getRepository(Site::class)->find($id);

            $site->setArchive(true);

            $em->persist($site);

            $em->flush();

            $this->addFlash("Success", "Le site a bien été supprimé !");



        return $this->RedirectToRoute('main');

    }

    /**
     * @param EntityManagerInterface $em
     * @Route("/adminSortie", name="adminArchivageSortie")
     * @isGranted("ROLE_ADMIN")
     * @return Response
     */
    public function archiveSortie(EntityManagerInterface $em)
    {

        $sorties = $em->getRepository(Sortie::class)->findAll();

        return $this->render('admin/adminSortie.html.twig', [
            'sorties' => $sorties,
        ]);

    }

    /**
     * @param EntityManagerInterface $em
     * @param Request $request
     * @Route("/adminSites/Add/", name="adminSitesAdd")
     * @isGranted("ROLE_ADMIN")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function adminSitesAdd(EntityManagerInterface $em, Request $request)
    {
        $site = new Site();
        $form = $this->createForm(AdminSitesUpdateType::class, $site);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $site->setArchive(false);
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
