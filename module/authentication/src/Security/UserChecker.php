<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication\Security;

use CfdiAdmin\Account\Entity\User;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->isLocked()) {
            $ex = new LockedException();
            $ex->setUser($user);
            throw $ex;
        }

        if (!$user->isEnabled()) {
            $ex = new DisabledException();
            $ex->setUser($user);
            throw $ex;
        }

        if ($user->isExpired()) {
            $ex = new AccountExpiredException();
            $ex->setUser($user);
            throw $ex;
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->isCredentialsExpired()) {
            $ex = new CredentialsExpiredException();
            $ex->setUser($user);
            throw $ex;
        }
    }
}
