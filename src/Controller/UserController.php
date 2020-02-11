<?php

namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/Details/{id}", name="userDetaisl")
     * @IsGranted("ROLE_USER")
     */
    public function userDetails()
    {
        return $this->render('user/userDetails.html.twig');
    }


    /**
     * @Route("/user/form/{id}", name="userForm")
     * @IsGranted("ROLE_USER")
     */
    public function userForm()
    {
        return $this->render('user/userForm.html.twig');
    }
}
