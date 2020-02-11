<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/form/{id}", name="userForm")
     */
    public function gestionProfil(EntityManagerInterface $em, Request $request, $id)
    {
        $user = $em->getRepository(User::class)->find($id);
        $form = $this->createForm( UserType::class, $user);
        //Traitement du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em->flush();
            $this->addFlash("success", "Profil updated or not !");
            return $this->redirectToRoute('base');
        }
        return $this->render("profil/formulaire.html.twig", [
            'userForm' => $form->createView()]);
    }

}
