<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication\Entity;

use Symfony\Component\Uid\Uuid;

class FailureLoginAttempt
{
    private Uuid $id;

    private string $ip;

    private ?string $username;

    private array $data = [];

    private \DateTime $createdAt;

    public function __construct(Uuid $id, string $ip, ?string $username, array $data)
    {
        $this->id = $id;
        $this->ip = $ip;
        $this->username = $username;
        $this->data = $data;
        $this->createdAt = new \DateTime('now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
