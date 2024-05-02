<?php

namespace App\Repository;

use App\Entity\PasswordReset;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method PasswordReset|null find($id, $lockMode = null, $lockVersion = null)
 * @method PasswordReset|null findOneBy(array $criteria, array $orderBy = null)
 * @method PasswordReset[]    findAll()
 * @method PasswordReset[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasswordResetRepository extends BaseRepository
{
    protected static string $entityClassName = PasswordReset::class;

    public function findByEmail(string $email): ?PasswordReset
    {
        try {
            $user = $this->createQueryBuilder('pr')
                ->where('pr.email = :email')
                ->setParameter('email', $email)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            $user = null;
        }

        return $user;
    }
}
