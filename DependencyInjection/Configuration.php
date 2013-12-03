<?php

namespace Ofertix\RabbitMqBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
            ->addDefaultsIfNotSet()
            ->append($this->setupConnections($rootNode))
            ->append($this->setupExchanges())
            ->append($this->setupQueues())
        ;

        return $treeBuilder;
    }

    protected function setupConnections(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()

            ->children()
                ->scalarNode('default_connection')
                    ->defaultNull()
                ->end()
            ->end()

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

        return $this->getConnectionsNode();
    }

    protected function getConnectionsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('connections');

        $node
            ->fixXmlConfig('connection')
            ->useAttributeAsKey('name')
            ->addDefaultChildrenIfNoneSet('default')

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

            ->beforeNormalization()
                ->always()
                ->then(function($value) {
                    if (empty($value)) {
                        $value = array('default' => array(), );
                    }

                    return $value;
                })
            ->end()
        ;

        return $node;
    }

    protected function setupExchanges()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('exchanges');
        $node
            ->fixXmlConfig('exchange')
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('type')
                        ->defaultValue('direct')
                        ->validate()
                            ->ifNotInArray(array('direct', 'fanout', 'topic', 'headers', ))
                            ->thenInvalid('Exchange type is invalid')
                        ->end()
                    ->end()
                    ->booleanNode('passive')
                        ->defaultValue(false)
                    ->end()
                    ->booleanNode('durable')
                        ->defaultValue(false)
                    ->end()
                    ->booleanNode('auto_delete')
                        ->defaultValue(true)
                    ->end()
                    ->booleanNode('internal')
                        ->defaultValue(false)
                    ->end()
                    ->booleanNode('nowait')
                        ->defaultValue(false)
                    ->end()
                    ->variableNode('arguments')
                        ->defaultNull()
                    ->end()
                    ->scalarNode('ticket')
                        ->defaultNull()
                    ->end()
        ;

        return $node;
    }

    protected function setupQueues()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('queues');
        $node
            ->fixXmlConfig('queue')
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->booleanNode('passive')
                        ->defaultValue(false)
                    ->end()
                    ->booleanNode('durable')
                        ->defaultValue(false)
                    ->end()
                    ->booleanNode('exclusive')
                        ->defaultValue(false)
                    ->end()
                    ->booleanNode('auto_delete')
                        ->defaultValue(true)
                    ->end()
                    ->booleanNode('nowait')
                        ->defaultValue(false)
                    ->end()
                    ->variableNode('arguments')
                        ->defaultNull()
                    ->end()
                    ->scalarNode('ticket')
                        ->defaultNull()
                    ->end()
        ;

        return $node;
    }
}
