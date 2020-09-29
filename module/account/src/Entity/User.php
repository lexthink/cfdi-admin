<?php

declare(strict_types=1);

namespace CfdiAdmin\Account\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

final class User implements UserInterface
{
    private Uuid $id;

    private string $email;

    private string $password;

    private ?string $locale;

    private bool $enabled;

    private bool $locked;

    private ?\DateTime $expiresAt;

    private ?\DateTime $credentialsExpiresAt;

    /**
     * Random string sent to the user email address in order to verify it.
     */
    private ?string $confirmationToken;

    private ?\DateTime $passwordRequestedAt;

    private ?\DateTime $lastLoginAt;

    private ?string $sessionId;

    private \DateTime $createdAt;

    public function __construct(
        Uuid $id,
        string $email,
        string $password,
        ?string $locale = null,
        bool $enabled = false
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->locale = $locale;
        $this->enabled = $enabled;
        $this->locked = false;
        $this->createdAt = new \DateTime('now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): void
    {
        $this->locked = $locked;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt instanceof \DateTime && $this->expiresAt->getTimestamp() < time();
    }

    public function setExpiration(?\DateTime $expiration): void
    {
        $this->expiresAt = $expiration;
    }

    public function isCredentialsExpired(): bool
    {
        return $this->credentialsExpiresAt instanceof \DateTime && $this->credentialsExpiresAt->getTimestamp() < time();
    }

    public function setCredentialsExpiration(?\DateTime $expiration): void
    {
        $this->credentialsExpiresAt = $expiration;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $token): void
    {
        $this->confirmationToken = $token;
    }

    /**
     * Checks whether the password reset request has expired.
     *
     * @param int $ttl Requests older than this many seconds will be considered expired
     */
    public function isPasswordRequestNonExpired(int $ttl): bool
    {
        return $this->passwordRequestedAt instanceof \DateTime &&
            $this->passwordRequestedAt->getTimestamp() + $ttl > time();
    }

    public function setPasswordRequestedAt(?\DateTime $requestedAt): void
    {
        $this->passwordRequestedAt = $requestedAt;
    }

    public function getLastLoginAt(): ?\DateTime
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTime $lastLoginAt): void
    {
        $this->lastLoginAt = $lastLoginAt;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(?string $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }
}
