<?php

namespace App\Controller;


use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use function Sodium\add;

class UserController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/User/Profile", name="userProfile")
     */
    public function userModif(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        //Traitement du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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
    public function userDetails(EntityManagerInterface $em,Request $request , $id)
    {
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->find($id);
        /*
        $currentUrl = $request->query->get('request_uri');
        $path = var_dump($currentUrl); ,
            'path' => $path ,$slug
*/
        return $this->render('user/userDetails.html.twig', ["user" => $user]);
    }


    /**
     * @Route("/Inscription/{id}", name="inscriptionSortie")
     * @param EntityManagerInterface $em
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function inscriptionSortie(EntityManagerInterface $em, $id)
    {
        $sortie = $em->getRepository(Sortie::class)->find($id);

        $user = $this->getUser();


        if ($sortie->isInscriptionPossible($user)){



            $sortie->addUser($user);


            if ($sortie->isCloturable()){
                $etat = $em->getRepository(Etat::class)->find(3);
                $sortie->setEtat($etat);
            }
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Vous êtes inscrit !');

        }else{


            $this->addFlash('danger', 'Trop tard déso');

        }






        return $this->redirectToRoute('main');
    }


    /**
     * @Route("/Desinscription/{id}", name="desinscriptionSortie")
     * @param EntityManagerInterface $em
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function desinscriptionSortie(EntityManagerInterface $em, $id)
    {

        $sortie = $em->getRepository(Sortie::class)->find($id);

        $user = $this->getUser();

        $sortie->removeUser($user);
        if($sortie->getEtat()->getLibelle() === Etat::CLOTUREE){
            $etat = $em->getRepository(Etat::class)->find(2);
            $sortie->setEtat($etat);
        }
        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('main');

    }


}
