<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

abstract class BaseRepository extends ServiceEntityRepository
{
    protected EntityManagerInterface $entityManager;
    protected static string $entityClassName;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager,)
    {
        parent::__construct($registry, static::$entityClassName);

        $this->entityManager = $entityManager;
    }

    public function getEntityManagerInstance(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function save($entity): void
    {
        $this->persist($entity);
        $this->apply();
    }

    public function persist($entity): void
    {
        $this->entityManager->persist($entity);
    }

    public function apply(): void
    {
        $this->entityManager->flush();
    }

    public function delete($entity): void
    {
        $this->entityManager->remove($entity);
    }

    public function remove($entity): void
    {
        $this->delete($entity);
        $this->apply();
    }

    public function deleteAll(string $entity): void
    {
        $builder = $this->entityManager->createQueryBuilder();

        $builder
            ->delete($entity, 'e')
            ->where(
                $builder->expr()->isNotNull('e.id')
            );

        $builder->getQuery()->execute();
    }
}
