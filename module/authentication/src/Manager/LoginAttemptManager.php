<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication\Manager;

use CfdiAdmin\Authentication\Entity\FailureLoginAttempt;
use CfdiAdmin\Authentication\Exception\BruteForceAttemptException;
use CfdiAdmin\Authentication\Repository\FailureLoginAttemptRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Uid\Uuid;

final class LoginAttemptManager
{
    private EntityManagerInterface $entityManager;

    private FailureLoginAttemptRepository $failureLoginAttemptRepository;

    private int $watchPeriod = 3600;

    public function __construct(
        EntityManagerInterface $entityManager,
        FailureLoginAttemptRepository $failureLoginAttemptRepository
    ) {
        $this->entityManager = $entityManager;
        $this->failureLoginAttemptRepository = $failureLoginAttemptRepository;
    }

    public function clearCountAttempts(Request $request, ?string $username): void
    {
        if (!$this->hasIp($request)) {
            return;
        }

        $this->failureLoginAttemptRepository->clearAttempts((string) $request->getClientIp(), $username);
    }

    public function countAttempts(Request $request, ?string $username): int
    {
        if (!$this->hasIp($request)) {
            return 0;
        }

        $startWatchDate = new \DateTime();
        $startWatchDate->modify('-'.$this->watchPeriod.' second');

        return $this->failureLoginAttemptRepository->countAttempts(
            (string) $request->getClientIp(),
            $username,
            $startWatchDate
        );
    }

    public function getLastAttemptDate(Request $request, ?string $username): ?\DateTime
    {
        if (!$this->hasIp($request)) {
            return null;
        }

        $lastAttempt = $this->failureLoginAttemptRepository->getLastAttempt(
            (string) $request->getClientIp(),
            $username
        );

        return null === $lastAttempt ? null : $lastAttempt->getCreatedAt();
    }

    public function incrementCountAttempts(
        Request $request,
        ?string $username,
        AuthenticationException $exception
    ): void {
        if (!$this->hasIp($request) || $exception instanceof BruteForceAttemptException) {
            return;
        }

        $data = [
            'exception' => $exception->getMessage(),
            'clientIp' => (string) $request->getClientIp(),
            'sessionId' => $request->getSession()->getId(),
        ];

        $attempt = new FailureLoginAttempt(Uuid::v4(), (string) $request->getClientIp(), $username, $data);

        $this->entityManager->persist($attempt);
        $this->entityManager->flush();
    }

    private function hasIp(Request $request): bool
    {
        return '' !== (string) $request->getClientIp();
    }
}
