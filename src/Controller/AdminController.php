<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/adminLieux", name="adminLieux")
     */
    public function adminLieux()
    {
        return $this->render('admin/adminLieux.html.twig');
    }

    /**
     * @Route("/adminSites", name="adminSites")
     */
    public function adminSites()
    {
        return $this->render('admin/adminSites.html.twig');
    }
}
