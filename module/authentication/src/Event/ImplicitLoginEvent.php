<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class ImplicitLoginEvent extends Event
{
    private Request $request;

    private TokenInterface $authenticationToken;

    public function __construct(Request $request, TokenInterface $authenticationToken)
    {
        $this->request = $request;
        $this->authenticationToken = $authenticationToken;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getAuthenticationToken(): TokenInterface
    {
        return $this->authenticationToken;
    }
}
