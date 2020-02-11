<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/Sortie/Create", name="sortieCreate")
     * @IsGranted("ROLE_USER")
     */
    public function sortieCreate()
    {
        return $this->render('sortie/sortieCreate.html.twig');
    }

    /**
     * @Route("/Sortie/Details/{id}", name="sortieDetails")
     * @IsGranted("ROLE_USER")
     */
    public function sortieDetails()
    {
        return $this->render('sortie/sortieDetails.html.twig');
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
