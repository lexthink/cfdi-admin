<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication\EventSubscriber;

use CfdiAdmin\Account\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;

class CheckPreviuosSessionSubscriber implements EventSubscriberInterface
{
    private TokenStorageInterface $tokenStorage;

    private Security $security;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        Security $security
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->security = $security;
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->checkPreviuosSession($event->getRequest()->getSession());
    }

    private function checkPreviuosSession(SessionInterface $session): void
    {
        if (null === $token = $token = $this->tokenStorage->getToken()) {
            return;
        }

        if (!$this->security->isGranted('IS_AUTHENTICATED_REMEMBERED') ||
            $this->security->isGranted('IS_IMPERSONATOR')
        ) {
            return;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return;
        }

        if ($user->getSessionId() !== $session->getId()) {
            // Kick this user out, because a new user has logged in
            $this->tokenStorage->setToken(null);

            // Tell the user that someone else has logged in with a different device
            $exception = new CustomUserMessageAuthenticationException(
                'Another device has logged in with your username and password.'
            );

            $session->set(Security::AUTHENTICATION_ERROR, $exception);
            throw $exception;
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onRequest',
        ];
    }
}
