<?php

namespace ShortCommands;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ShortCommandsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterShortCommandsCompilerPass());
        parent::build($container);
    }
}