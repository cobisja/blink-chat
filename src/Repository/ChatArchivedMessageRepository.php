<?php

namespace App\Repository;

use App\Entity\ChatArchivedMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChatArchivedMessage>
 *
 * @method ChatArchivedMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatArchivedMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatArchivedMessage[]    findAll()
 * @method ChatArchivedMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatArchivedMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatArchivedMessage::class);
    }

    //    /**
    //     * @return ChatArchivedMessage[] Returns an array of ChatArchivedMessage objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ChatArchivedMessage
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
