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
        $lieuRepo = $em->getRepository(Lieu::class);
        $lieux = $lieuRepo->findAll();

        return $this->render('admin/adminListeLieux.html.twig',
            ['lieux' => $lieux]);
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
        $lieuRepo = $em->getRepository(Lieu::class);
        $lieux = $lieuRepo->find($id);

        $lieuForm = $this->createForm(LieuType::class, $lieux);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {

            $em->persist($lieux);
            $em->flush();

            $this->addFlash("success", "modification effectuée");

            return $this->redirectToRoute('adminListeLieux');
        }

        return $this->render('admin/adminModifLieux.html.twig', [
            'lieuForm' => $lieuForm->createView(),
        ]);
    }

    /**
     * @Route("/admin/adminAjoutLieux", name="adminAjoutLieux")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminAjoutLieux(EntityManagerInterface $em, Request $request)
    {
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $em->persist($lieu);
            $em->flush();

            $this->addFlash("success", "ajout effectuée");

            return $this->redirectToRoute('adminListeLieux');
        }

        return $this->render('admin/adminAjoutLieux.html.twig', [
            'lieuForm' => $lieuForm->createView(),
        ]);
    }

    /**
     * @Route("/admin/adminDeleteLieux/{id}", name="adminDeleteLieux")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminDeleteLieux(EntityManagerInterface $em, $id)
    {
        $lieu = $em->getRepository(Lieu::class)->find($id);
        $em->remove($lieu);
        $em->flush();
        $this->addFlash("success", "lieu delete");
        return $this->redirectToRoute("adminListeLieux");
    }
}