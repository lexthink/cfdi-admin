<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication\Security;

use CfdiAdmin\Account\Entity\User;
use CfdiAdmin\Account\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        if (empty($username)) {
            throw new UsernameNotFoundException('Empty username');
        }

        $user = $this->userRepository->findByEmail($username);

        if ($user instanceof User) {
            return $user;
        }

        throw new UsernameNotFoundException(sprintf('Username "%s" not found', $username));
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}
