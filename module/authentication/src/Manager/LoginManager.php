<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication\Manager;

use CfdiAdmin\Authentication\Event\ImplicitLoginEvent;
use CfdiAdmin\Authentication\Security\UserChecker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class LoginManager
{
    private RequestStack $requestStack;

    private UserChecker $userChecker;

    private SessionAuthenticationStrategyInterface $sessionStrategy;

    private TokenStorageInterface $tokenStorage;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        RequestStack $requestStack,
        UserChecker $userChecker,
        SessionAuthenticationStrategyInterface $sessionStrategy,
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->requestStack = $requestStack;
        $this->userChecker = $userChecker;
        $this->sessionStrategy = $sessionStrategy;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function authenticate(string $firewallName, UserInterface $user, Request $request = null): void
    {
        $request ??= $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \LogicException('A Request must be available.');
        }

        $this->userChecker->checkPreAuth($user);

        $token = $this->createToken($firewallName, $user);

        $this->sessionStrategy->onAuthentication($request, $token);

        $this->tokenStorage->setToken($token);

        $this->eventDispatcher->dispatch(new ImplicitLoginEvent($request, $token));
    }

    private function createToken(string $firewallName, UserInterface $user): TokenInterface
    {
        return new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
    }
}
