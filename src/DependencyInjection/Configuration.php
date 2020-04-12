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
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('short_commands');
        $rootNode
            ->children()
            ->arrayNode('directories')
            ->scalarPrototype();
        return $treeBuilder;
    }
}