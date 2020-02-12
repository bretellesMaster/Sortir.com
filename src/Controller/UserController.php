<?php

namespace App\Controller;


use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/User/Profile", name="userProfile")
     */
    public function userModif(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        $form = $this->createForm( UserType::class, $user);
        //Traitement du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            //modification du password
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);


            $em->persist($user);
            $em->flush();
            $this->addFlash("success", "Profil updated !");
            return $this->redirectToRoute('userProfile');
        }
        return $this->render("user/userForm.html.twig", [
            'userForm' => $form->createView()]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/User/Details/{id}", name="userDetails")
     */
    public function userDetails(EntityManagerInterface $em, $id)
    {
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->find($id);
        return $this->render('user/userDetails.html.twig', ["user" => $user]);
    }


}
