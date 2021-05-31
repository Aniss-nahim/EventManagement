<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Event[]
     */
    public function findAllOrderByCreatedAt() : array
    {
        $qb = $this->createQueryBuilder('e');
        
        $qb = $qb->innerJoin('App\Entity\User', 'u', Join::WITH, 'u = e.owner')
            ->innerJoin('App\Entity\EventTag', 'et', Join::WITH, 'et.taggedEvent = e')
            ->innerJoin('App\Entity\Tag', 't', Join::WITH, 't = et.tag')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->in('e.state', array("Published", "Re-published")),
                ))
            ->orderBy('e.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * @return Event
     */
    public function findOneWithAll($eventId)
    {
        $qb = $this->createQueryBuilder('e');
        
        $qb = $qb->innerJoin('App\Entity\User', 'u', Join::WITH, 'u = e.owner')
            ->innerJoin('App\Entity\EventTag', 'et', Join::WITH, 'et.taggedEvent = e')
            ->innerJoin('App\Entity\Tag', 't', Join::WITH, 't = et.tag')
            ->where("e.id = :eventId")
            ->setParameter(":eventId", $eventId);
        
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Event filtering with query builder
     * @param startsOn // done
     * @param endsOn // done
     * @param title // done
     * @param city // done
     * @param tgas // done
     * @param type // done
     * @return Event[]
     */
    public function filterEvent(array $query) : array
    {
        $qb = $this->createQueryBuilder('e');
 
        // Only Published events
        $qb->where("e.state = 'Published'");

        // Join with User table
        $qb = $qb->innerJoin('App\Entity\User', 'u', Join::WITH, 'u = e.owner')
            ->innerJoin('App\Entity\EventTag', 'et', Join::WITH, 'et.taggedEvent = e')
            ->innerJoin('App\Entity\Tag', 't', Join::WITH, 't = et.tag');

        // Query by startDate
        if(!empty($query['startDate'])){
            $qb = $qb->andWhere('e.startDate >= :startDate')
                ->setParameter('startDate', $query['startDate']);
        }

        // Query by endDate
        if(!empty($query['endDate'])){
            $qb = $qb->andWhere('e.endDate <= :endDate')
                ->setParameter('endDate', $query['endDate']);
        }

        // Query by title
        if(!empty($query['title'])){
            $qb = $qb->andWhere("e.title like :title")
                ->setParameter('title', $query['title'].'%');
        }

        // Query by type
        if(!empty($query['type'])){
            $qb = $qb->andWhere('e.type = :type')
                ->setParameter('type', $query['type']);
        }

        // Query by city
        if(!empty($query['city'])){
            $qb = $qb->andWhere('e.city = :city')
                ->setParameter('city', $query['city']);
        }

        // Query by tags
        if(!empty($query['tags'])){
            $em = $this->getEntityManager();
            $subqb = $em->createQueryBuilder();
            // select event ids with at least one tag in query["tags"]
            $subqb->select('e.id')
                ->from('App\Entity\EventTag', 'et')
                ->innerJoin('App\Entity\Tag', 't', Join::WITH, 'et.tag = t')
                ->innerJoin('App\Entity\Event', 'e', Join::WITH, 'et.taggedEvent = e')
                ->where('t.tagName in ( :queryTags )')
                ->groupBy('e')
                ->setParameter('queryTags', $query['tags']);
            $eventIds = $subqb->getQuery()->getResult();

            // filter only event who have one of those tags
            $qb = $qb->andWhere('e.id in ( :eventIds )')
                ->setParameter(':eventIds', $eventIds);
        }

        // Query by rating
        if(!empty($query['rating'])){
            
        }

        // Ordering by field
        if(!empty($query['orderBy']) && !empty($query['order'])){
            $order = new OrderBy('e.'.$query['orderBy'], $query['order']);
            $qb = $qb->orderBy($order);
        }

        return $qb->getQuery()->getResult();
    }


    /**
     * Query user unpublished event i.e events with state Created
     * @return Event[]
     */
    public function findUserUnpublishedEvents($userId) : array
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e')
            ->innerJoin('App\Entity\User', 'u', Join::WITH, 'e.owner = u')
            ->where('u.id = :userId')
            ->andWhere("e.state = 'Created'")
            ->setParameter(':userId', $userId)
            ->orderBy('e.createdAt', 'desc');

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
