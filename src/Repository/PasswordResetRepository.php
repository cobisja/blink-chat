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
            $passwordReset = $this->createQueryBuilder('pr')
                ->where('pr.email = :email')
                ->setParameter('email', $email)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            $passwordReset = null;
        }

        return $passwordReset;
    }

    public function findByToken(string $token): ?PasswordReset
    {
        try {
            $passwordReset = $this->createQueryBuilder('pr')
                ->where('pr.token = :token')
                ->setParameter('token', $token)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            $passwordReset = null;
        }

        return $passwordReset;
    }
}
