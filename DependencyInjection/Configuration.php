<?php

namespace Ofertix\RabbitMqBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    protected $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->alias);

        $rootNode
            ->canBeDisabled()

            ->children()
                ->scalarNode('default_connection')
                    ->defaultNull()
                ->end()
            ->end()

            ->fixXmlConfig('connection')
            ->append($this->getConnectionsNode())

            ->validate()
                ->ifTrue(function($value) {
                    return null === $value['default_connection'];
                })
                ->then(function($value) {
                    list($value['default_connection'], ) = each($value['connections']);

                    return $value;
                })
            ->end()

            ->validate()
                ->ifTrue(function($value) {
                    return null !== $value['default_connection'] && !isset($value['connections'][$value['default_connection']]);
                })
                ->then(function($value) {
                    throw new \InvalidArgumentException(sprintf(
                        'The default connection should be named "%s" but it does not exist',
                        $value['default_connection']
                    ));
                })
            ->end()
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
