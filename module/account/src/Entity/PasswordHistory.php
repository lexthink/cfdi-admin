<?php

declare(strict_types=1);

namespace CfdiAdmin\Account\Entity;

use Symfony\Component\Uid\Uuid;

final class PasswordHistory
{
    private Uuid $id;

    private User $user;

    private string $password;

    private \DateTime $createdAt;

    public function __construct(Uuid $id, User $user, string $password)
    {
        $this->id = $id;
        $this->user = $user;
        $this->password = $password;
        $this->createdAt = new \DateTime('now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
