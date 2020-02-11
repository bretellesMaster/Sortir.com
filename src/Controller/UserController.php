<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/Details/{id}", name="userDetaisl")
     */
    public function userDetails()
    {
        return $this->render('user/userDetails.html.twig');
    }


    /**
     * @Route("/user/form/{id}", name="userForm")
     */
    public function userForm()
    {
        return $this->render('user/userForm.html.twig');
    }
}
