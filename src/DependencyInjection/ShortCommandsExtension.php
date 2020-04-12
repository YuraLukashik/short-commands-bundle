<?php

namespace ShortCommands\DependencyInjection;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ShortCommandsExtension extends Extension
{
    private $processedConfig;

    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processedConfig = $this->processConfiguration($configuration, $configs);
        return $this->processedConfig;
    }

    public function commandsDirectories()
    {
        return $this->processedConfig['directories'] ?? [];
    }
}