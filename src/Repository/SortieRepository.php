<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\Date;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param $filtre
     * @param $user
     * @return mixed
     */
    public function filtre($filtre, $user)
    {
        $qb = $this->createQueryBuilder('s');


        if (!empty($filtre['site'])) {
            $qb->where('s.site = :site')
                ->setParameter('site', $filtre['site']);

        }
        if (!empty($filtre['search'])) {
            $qb->andWhere('s.nom LIKE :search')
                ->setParameter('search', '%' . $filtre['search'] . '%');
        }
        if (!empty($filtre['dateDebut']) && !empty($filtre['dateFin'])) {
            $qb->andWhere('s.dateHeureDebut > :dateDebut')
                ->andWhere('s.dateHeureDebut < :dateFin')
                ->setParameter('dateDebut', $filtre['dateDebut'])
                ->setParameter('dateFin', $filtre['dateFin']);
        }

        if($filtre['checkbox1'] == 1){
            $qb->andWhere('s.organisateur = :user');
            $qb->setParameter('user', $user);
            dump($user);

        }

        if(!empty($filtre['checkbox2'])){
            $qb->andWhere($qb->expr()->isMemberOf(':user', 's.users'))
                ->setParameter('user', $user);


        }

        if(!empty($filtre['checkbox3'])){
            $qb->andWhere(':user NOT MEMBER OF s.users')
                ->setParameter('user', $user);

        }

        if(!empty($filtre['checkbox4'])){
            $qb->andWhere(':s.etat = :etat');


        }
        $query = $qb->getQuery();
        $result = $query->execute();

        return $result;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getSortieOuverte(){
        $em = $this->getEntityManager();
        $etat1 = $em->getRepository(Etat::class)->find(2);
        $etat2 = $em->getRepository(Etat::class)->find(3);


        $qb = $this->createQueryBuilder('s');
        $qb->where('s.dateHeureDebut < :dateDuJour')
            ->orWhere('s.etat = :etat1')
            ->orWhere('s.etat = :etat2')
            ->setParameter('dateDuJour', new \DateTime())
            ->setParameter('etat1', $etat1)
            ->setParameter('etat2', $etat2);

        $query = $qb->getQuery();
        $result = $query->execute();

        return $result;

    }


}
