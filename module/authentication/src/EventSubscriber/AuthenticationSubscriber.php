<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication\EventSubscriber;

use CfdiAdmin\Account\Entity\User;
use CfdiAdmin\Authentication\Event\ImplicitLoginEvent;
use CfdiAdmin\Authentication\Manager\LoginAttemptManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class AuthenticationSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    private SessionInterface $session;

    private LoginAttemptManager $loginAttemptManager;

    private EntityManagerInterface $entityManager;

    public function __construct(
        RequestStack $requestStack,
        SessionInterface $session,
        LoginAttemptManager $loginAttemptManager,
        EntityManagerInterface $entityManager
    ) {
        $this->requestStack = $requestStack;
        $this->session = $session;
        $this->loginAttemptManager = $loginAttemptManager;
        $this->entityManager = $entityManager;
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            $this->loginAttemptManager->incrementCountAttempts(
                $request,
                $request->get('_username'),
                $event->getAuthenticationException()
            );
        }
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $request = $event->getRequest();
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof User) {
            $this->onAuthenticationSuccess($user, $request);
        }
    }

    public function onImplicitLogin(ImplicitLoginEvent $event): void
    {
        $request = $event->getRequest();
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof User) {
            $this->onAuthenticationSuccess($user, $request);
        }
    }

    private function onAuthenticationSuccess(User $user, Request $request): void
    {
        if (null !== $user->getLocale()) {
            $this->session->set('_locale', $user->getLocale());
        }

        $user->setSessionId($this->session->getId());
        $user->setLastLoginAt(new \DateTime());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->loginAttemptManager->clearCountAttempts($request, $user->getUsername());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationFailureEvent::class => 'onAuthenticationFailure',
            InteractiveLoginEvent::class => 'onInteractiveLogin',
            ImplicitLoginEvent::class => 'onImplicitLogin',
        ];
    }
}
