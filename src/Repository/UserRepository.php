<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends BaseRepository implements PasswordUpgraderInterface
{
    protected static string $entityClassName = User::class;

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findByEmail(string $email): ?User
    {
        try {
            $user = $this->createQueryBuilder('u')
                ->where('u.email = :email')
                ->setParameter('email', $email)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            $user = null;
        }

        return $user;
    }

    public function findByNickname(string $nickname): ?User
    {
        try {
            $user = $this->createQueryBuilder('u')
                ->where('u.nickname = :nickname')
                ->setParameter('nickname', $nickname)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            $user = null;
        }

        return $user;
    }

    /**
     * @return QueryBuilder|User[]
     */
    public function findBySearchQuery(
        ?string $query,
        ?string $sort = null,
        bool $returnQueryBuilder = false
    ): QueryBuilder|array {
        $queryBuilder = $this->createQueryBuilder('u');

        if ($query) {
            $queryBuilder
                ->where("u.queryField LIKE :query")
                ->setParameter('query', '%' . $query . '%');
        }

        if ($sort) {
            $sort = in_array(strtoupper($sort), [Order::Ascending->value, Order::Descending->value])
                ? $sort
                : Order::Ascending->value;

            $queryBuilder
                ->orderBy("u.name", $sort)
                ->addOrderBy("u.lastname", $sort);
        }

        if ($returnQueryBuilder) {
            return $queryBuilder;
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
