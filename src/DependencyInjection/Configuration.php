<?php

namespace ShortCommands\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('short_commands');
        $treeBuilder
            ->getRootNode()
            ->children()
            ->arrayNode('directories')
            ->scalarPrototype();
        return $treeBuilder;
    }
}