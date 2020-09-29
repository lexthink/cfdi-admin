<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication\Security;

use CfdiAdmin\Authentication\Manager\LoginAttemptManager;
use Symfony\Component\HttpFoundation\Request;

final class BruteForceChecker
{
    private LoginAttemptManager $loginAttemptManager;

    private array $options = [
        'max_count_attempts' => 3,
        'timeout' => 600,
    ];

    public function __construct(LoginAttemptManager $loginAttemptManager)
    {
        $this->loginAttemptManager = $loginAttemptManager;
    }

    public function canLogin(Request $request): bool
    {
        $username = $request->get('_username');

        if ($this->loginAttemptManager->countAttempts($request, $username) >= $this->options['max_count_attempts']) {
            if (null !== $lastAttemptDate = $this->loginAttemptManager->getLastAttemptDate($request, $username)) {
                $dateAllowLogin = $lastAttemptDate->modify('+'.$this->options['timeout'].' second');
                if (1 === $dateAllowLogin->diff(new \DateTime())->invert) {
                    return false;
                }
            }
        }

        return true;
    }
}
