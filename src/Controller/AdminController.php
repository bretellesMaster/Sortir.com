<?php

namespace App\Controller;

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
    public function adminSites()
    {
        return $this->render('admin/adminSites.html.twig');
    }
}
