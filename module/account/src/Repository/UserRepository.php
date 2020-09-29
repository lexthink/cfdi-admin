<?php

declare(strict_types=1);

namespace CfdiAdmin\Account\Repository;

use CfdiAdmin\Account\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findById(Uuid $id): ?User
    {
        $object = $this->find($id);

        if (!$object instanceof User) {
            return null;
        }

        return $object;
    }

    public function findByEmail(string $email): ?User
    {
        $object = $this->findOneBy(['email' => $email]);

        if (!$object instanceof User) {
            return null;
        }

        return $object;
    }

    public function findByConfirmationToken(string $token): ?User
    {
        $object = $this->findOneBy(['confirmationToken' => $token]);

        if (!$object instanceof User) {
            return null;
        }

        return $object;
    }
}
