<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication;

use App\AbstractModule;
use CfdiAdmin\Authentication\DependencyInjection\Compiler\Authentication;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CfdiAdminAuthenticationBundle extends AbstractModule
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new Authentication());
    }
}
