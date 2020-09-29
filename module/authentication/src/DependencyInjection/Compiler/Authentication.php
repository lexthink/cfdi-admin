<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication\DependencyInjection\Compiler;

use CfdiAdmin\Authentication\Security\BruteForceChecker;
use CfdiAdmin\Authentication\Security\UsernamePasswordFormAuthenticationListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class Authentication implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container
            ->getDefinition('security.authentication.listener.form')
            ->setClass(UsernamePasswordFormAuthenticationListener::class)
            ->addMethodCall(
                'setEventDispatcher',
                [
                    new Reference('event_dispatcher'),
                ]
            )
            ->addMethodCall(
                'setBruteForceChecker',
                [
                    new Reference(BruteForceChecker::class),
                ]
            );
    }
}
