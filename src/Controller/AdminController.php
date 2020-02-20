<?php

namespace App\Controller;


use App\Entity\Site;
use App\Entity\Sortie;
use App\Form\AdminSitesUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

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
    public function adminSites(EntityManagerInterface $em,Request $request)
    {
        $repsites = $em->getRepository(Site::class);
        $sites=$repsites->findAll();
        $repsites = $em->getRepository(Site::class);
        $sitesTrier=$repsites->findAll();

        return $this->render('admin/adminSites.html.twig', ["sites"=>$sites,
            'sitesTriers'=>$sitesTrier]);
    }

    /*
     * @IsGranted("ROLE_ADMIN")
     * @Route("/adminSites/Filtre", name="filtreSites")
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     *

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
    public function adminSitesUpdate(EntityManagerInterface $em,$id,Request $request){

        $siteclass= new Site();
        $form = $this->createForm(AdminSitesUpdateType::class,$siteclass);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $siteRepository=$em->getRepository(Site::class);
            $site=$siteRepository->find($id);

            $nomSite = $form->get('nom')->getViewData();
            $site->setNom($nomSite);

            $em->persist($site);

            $em->flush();

            $this->addFlash("Success","Votre site a bien été modifié !");

            return $this->RedirectToRoute("adminSites");}

        return $this-> render('admin/siteUpdate.html.twig',[
            'siteForm'=>$form->createView()
        ]);
    }


    /**
     * @Route("/adminSites/Delete/{id}",name="adminSitesDelete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminSitesDelete(EntityManagerInterface $em,$id){

        $site=$em->getRepository(Site::class)->find($id);

        if(!$id){
            return $this->RedirectToRoute("main");
        }

        $em->remove($site);
        $em->flush();

        $siteRepository = $em->getRepository(Site::class);
        $sites = $siteRepository->findAll();

        $this->addFlash("Success","Le site a bien été supprimé !");

        return $this-> render('admin/adminSites.html.twig',["sites"=>$sites]);
    }

    /**
     * @Route("/adminSortie", name="adminArchivageSortie")
     * @isGranted("ROLE_ADMIN")
     */
    public function archiveSortie (EntityManagerInterface $em){

        // on récupère toutes les sorties
        $sorties = $em->getRepository(Sortie::class)->findAll();

        // boucle pour control
        foreach ($sorties as $sortie ) {
                //récupération de l'état de la sortie
                $etat = $sortie->getEtat();
                // on recupere l'intervalle entre la date du jour et la date du debut de la sortie
                $interval = date_diff(new \DateTime(), $sortie->getDateHeureDebut());

                // si l'intervalle est superieur à 30jours et à l'état "annulé" ou "passée"
                if ( ($interval->days > 30) && ($etat =='Annulé' or $etat=='Passée')) {
                    $sortie->setArchive(true);
                    $em->persist($sortie);
                    $em->flush();
                }
            }
        $this ->addFlash("success", "Sortie(s) archivée(s)" );
        return $this->redirectToRoute('adminArchiSortieList');
    }

    /**
     * @Route("/adminSortie/list", name="adminArchiSortieList")
     * @isGranted("ROLE_ADMIN")
     */
    public function listSortiePourArchivage (EntityManagerInterface $em){
        $sorties = $em->getRepository(Sortie::class)->findAll();

        return $this->render('admin/adminSortie.html.twig', [
            'sorties' => $sorties,
        ]);
    }

    /**
     * @Route("/adminSortie/listeSortieArchivée", name="adminListSortieArchivée")
     * @isGranted("ROLE_ADMIN")
     */
    public function listSortieArchivée (EntityManagerInterface $em){
        $sorties = $em->getRepository(Sortie::class)->findBy([
            'archive' => true,
        ]);

        return $this->render('sortie/sortieArchivéeList.html.twig', [
            'sorties' => $sorties,
        ]);
    }

    /**
     * @Route("/adminSites/Add/", name="adminSitesAdd")
     */
    public function adminSitesAdd(EntityManagerInterface $em, Request $request){
        $site= new Site();
        $form = $this->createForm(AdminSitesUpdateType::class,$site);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $nomSite = $form->get('nom')->getViewData();
            $site->setNom($nomSite);

            $em->persist($site);

            $em->flush();

            $this->addFlash("Success", "Votre site a bien été modifié !");

            return $this->RedirectToRoute("adminSites");
        }

            return $this->render('admin/siteUpdate.html.twig',[
                'siteForm'=>$form->createView()
            ]);
    }


}
