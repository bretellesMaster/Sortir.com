<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{
    /**
     * @Route("/main", name="main")
     * @IsGranted("ROLE_USER")
     */
    public function index(EntityManagerInterface $em)
    {
        $sorties = $em->getRepository(Sortie::class)->getSortieOuverte();
        $dateDuJour = new \DateTime();
        foreach($sorties as $sortie){
            if($sortie->getDateLimiteInscription() < $dateDuJour){
                $sortie->setEtat($em->getRepository(Etat::class)->find(3));
                $em->persist($sortie);
                $em->flush();
            }


        }


        $sites = $em->getRepository(Site::class)->findAll();


//        dump($sorties);die();

        return $this->render('main/index.html.twig', [
            'sorties' => $sorties,
            'sites'=>$sites,
        ]);
    }
}
