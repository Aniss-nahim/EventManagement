<?php

namespace App\Repository;

use App\Entity\Participation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\OrderBy;
use App\Entity\Event;
use App\Entity\User;

/**
 * @method Participation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participation[]    findAll()
 * @method Participation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participation::class);
    }

    /**
     * Find participations by user and type
     * @return Particupation[]
     */
    public function findByUserAndType($userId, $participationType)
    {
        $qb = $this->createQueryBuilder('p');
        
        $qb = $qb->innerJoin('App\Entity\User', 'u', Join::WITH, 'u = p.participantUser')
            ->innerJoin('App\Entity\Event', 'e', Join::WITH, 'p.participatedEvent = e')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('p.type', $participationType),
                    $qb->expr()->eq('p.participantUser.id', $userId)
                ),
            )->orderBy('e.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Participation[] Returns an array of Participation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Participation
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
