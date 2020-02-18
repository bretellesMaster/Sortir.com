<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\VilleType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AdminController extends AbstractController
{
    /**
     * @Route("/admin/adminListeLieux", name="adminListeLieux")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminListelieux(EntityManagerInterface $em)
    {
        $villeRepo = $em->getRepository(Ville::class);
        $villes = $villeRepo->findAll();

        return $this->render('admin/adminListeLieux.html.twig',
            ['villes' => $villes]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin2", name="filtreAdmin")
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function filtreAdmin (EntityManagerInterface $em, Request $request)
    {
        $rep = $em->getRepository(Ville::class)->findAll();

        $filter = [
            'search' => $request->get('search'),
        ];

        $user = $this->getUser();

        $villes = $rep->filtre($filter, $user);

        return $this->render('admin/adminListeLieux.html.twig',
            ['villes' => $villes]);
    }

    /**
     * @Route("/admin/adminModifLieux/{id}", name="adminModifLieux")
     * @IsGranted("ROLE_ADMIN")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function adminModifLieux(EntityManagerInterface $em, Request $request, $id)
    {
        $villeRepo = $em->getRepository(Ville::class);
        $villes = $villeRepo->find($id);

        $villeForm = $this->createForm(VilleType::class, $villes);
        $villeForm->handleRequest($request);

        if ($villeForm->isSubmitted() && $villeForm->isValid()) {

            $em->persist($villes);
            $em->flush();

            $this->addFlash("success", "modification effectuée");

            return $this->redirectToRoute('main');
        }

        return $this->render('admin/adminModifLieux.html.twig', [
            'villeForm' => $villeForm->createView(),
        ]);
    }
}