<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication\Security;

use CfdiAdmin\Authentication\Event\BruteForceAttemptEvent;
use CfdiAdmin\Authentication\Exception\BruteForceAttemptException;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener as BaseListener;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UsernamePasswordFormAuthenticationListener extends BaseListener
{
    protected EventDispatcherInterface $eventDispatcher;

    protected BruteForceChecker $bruteForceChecker;

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function setBruteForceChecker(BruteForceChecker $bruteForceChecker): void
    {
        $this->bruteForceChecker = $bruteForceChecker;
    }

    public function getBruteForceChecker(): BruteForceChecker
    {
        return $this->bruteForceChecker;
    }

    public function authenticate(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$this->getBruteForceChecker()->canLogin($request)) {
            $this->getEventDispatcher()->dispatch(new BruteForceAttemptEvent($event, $this->getBruteForceChecker()));

            $exception = new BruteForceAttemptException('Brute force attempt');
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

            throw $exception;
        }

        parent::authenticate($event);
    }
}
