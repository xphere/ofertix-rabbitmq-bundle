<?php

namespace Ofertix\RabbitMqBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ofertix_rabbit_mq');

        $rootNode
            ->fixXmlConfig('connection')
            ->append($this->getConnectionsNode())
        ;

        return $treeBuilder;
    }

    protected function getConnectionsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('connections');

        $node
            ->addDefaultChildrenIfNoneSet('default')
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('host')
                        ->defaultValue('localhost')
                    ->end()
                    ->integerNode('port')
                        ->defaultValue(5672)
                    ->end()
                    ->scalarNode('user')
                        ->defaultValue('guest')
                    ->end()
                    ->scalarNode('password')
                        ->defaultValue('guest')
                    ->end()
                    ->scalarNode('vhost')
                        ->defaultValue('/')
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
