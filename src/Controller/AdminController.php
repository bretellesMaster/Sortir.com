<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     */
    public function adminSites(EntityManagerInterface $em)
    {   $villeRepository = $em->getRepository(Ville::class);
        $villes = $villeRepository->findAll();
        $sortieRepository = $em->getRepository(Sortie::class);
        $sorties = $sortieRepository->findAll();
        return $this->render('admin/adminSites.html.twig',
            ["villes"=>$villes,
                "sorties"=>$sorties]);
    }

    /**
     * @Route("/adminSites/Update/{id}",name="adminSitesUpdate")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminSitesUpdate(EntityManagerInterface $em,$id){

        $siteRepository=$em->getRepository(Site::class);
        $site=$siteRepository->findOneBy(["id"=>$id]);

        if (isset($_POST['site'])!=null ) {
            $site->setNom($_POST['site']);
            return $this->RedirectToRoute("adminSites");}


        return $this-> render('admin/siteUpdate.html.twig');
    }


    /**
     * @Route("/adminSites/Delete/{id}",name="adminSitesDelete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminSitesDelete(EntityManagerInterface $em,$id){


        return $this-> render('admin/adminSites.html.twig');
    }
}
