<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication\Event;

use CfdiAdmin\Authentication\Security\BruteForceChecker;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Contracts\EventDispatcher\Event;

final class BruteForceAttemptEvent extends Event
{
    private RequestEvent $requestEvent;

    private BruteForceChecker $bruteForceChecker;

    public function __construct(RequestEvent $requestEvent, BruteForceChecker $bruteForceChecker)
    {
        $this->requestEvent = $requestEvent;
        $this->bruteForceChecker = $bruteForceChecker;
    }

    public function getRequestEvent(): RequestEvent
    {
        return $this->requestEvent;
    }

    public function getBruteForceChecker(): BruteForceChecker
    {
        return $this->bruteForceChecker;
    }
}
