<?php

namespace App\Controller;


use App\Entity\Etat;
use App\Entity\Sortie;
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


    /**
     * @Route("/Inscription/{id}", name="inscriptionSortie")
     * @param EntityManagerInterface $em
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function inscriptionSortie(EntityManagerInterface $em, $id){
        $sortie = $em->getRepository(Sortie::class)->find($id);

        $user = $this->getUser();
        $nb = $sortie->getUsers()->count();
        dump($nb);
        dump($sortie->getNbInscriptionsMax());

        if ($sortie->getUsers()->contains($user)){
            $this->addFlash("danger", "Vous êtes déjà inscrit");
        }

        if ($sortie->getUsers()->count() < $sortie->getNbInscriptionsMax()) {
            $sortie->addUser($user);


            $nbMax = $sortie->getNbInscriptionsMax();
            $nb = $sortie->getUsers()->count();

            if($nb == $nbMax){
                $sortie = $em->getRepository(Sortie::class)->find($id);
                $etat = $em->getRepository(Etat::class)->find(3);
                $sortie->setEtat($etat);
                $em->persist($sortie);
            }
            $em->persist($sortie);


            $em->flush();

            $this->addFlash("success", 'Vous êtes bien inscrit à l\'évenement : '.$sortie->getNom());
        }

        return $this->redirectToRoute('main');
    }


    /**
     * @Route("/Desinscription/{id}", name="desinscriptionSortie")
     * @param EntityManagerInterface $em
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function desinscriptionSortie(EntityManagerInterface $em, $id){

        $sortie = $em->getRepository(Sortie::class)->find($id);
        $etat = $em->getRepository(Etat::class)->find(3);
        $user = $this->getUser();

        $sortie->removeUser($user);
        if($sortie->getEtat()->getId() === $etat->getId()){
            $etat = $em->getRepository(Etat::class)->find(2);
            $sortie->setEtat($etat);
        }
        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('main');

    }






}
