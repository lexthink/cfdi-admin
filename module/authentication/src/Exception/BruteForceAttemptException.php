<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class BruteForceAttemptException extends AuthenticationException
{
    public function getMessageKey(): string
    {
        return 'Too many authentication failures.';
    }
}
