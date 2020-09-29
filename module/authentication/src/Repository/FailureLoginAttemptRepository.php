<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication\Repository;

use CfdiAdmin\Authentication\Entity\FailureLoginAttempt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class FailureLoginAttemptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FailureLoginAttempt::class);
    }

    public function countAttempts(string $ip, ?string $username, \DateTime $startDate): int
    {
        $queryBuilder = $this->createQueryBuilder('attempt')
            ->select('COUNT(attempt.id)')
            ->where('attempt.ip = :ip')
            ->andWhere('attempt.createdAt > :createdAt')
            ->setParameters([
                'ip' => $ip,
                'createdAt' => $startDate,
            ]);

        if (null === $username) {
            $queryBuilder->andWhere('attempt.username IS NULL');
        } else {
            $queryBuilder->andWhere('attempt.username = :username')->setParameter('username', $username);
        }

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function getLastAttempt(string $ip, ?string $username): ?FailureLoginAttempt
    {
        $queryBuilder = $this->createQueryBuilder('attempt')
            ->where('attempt.ip = :ip')
            ->orderBy('attempt.createdAt', 'DESC')
            ->setParameters([
                'ip' => $ip,
            ]);

        if (null === $username) {
            $queryBuilder->andWhere('attempt.username IS NULL');
        } else {
            $queryBuilder->andWhere('attempt.username = :username')->setParameter('username', $username);
        }

        return $queryBuilder->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }

    public function clearAttempts(string $ip, ?string $username): void
    {
        $queryBuilder = $this->createQueryBuilder('attempt')
            ->delete()
            ->where('attempt.ip = :ip')
            ->setParameters([
                'ip' => $ip,
            ]);

        if (null === $username) {
            $queryBuilder->andWhere('attempt.username IS NULL');
        } else {
            $queryBuilder->andWhere('attempt.username = :username')->setParameter('username', $username);
        }

        $queryBuilder->getQuery()->execute();
    }
}
