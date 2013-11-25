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
        $rootNode->canBeDisabled();
        $this->setupConnections($rootNode);
        $this->setupExchanges($rootNode);
        $this->setupQueues($rootNode);

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
                ->append($this->getConnectionsNode())
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
    }

    protected function getConnectionsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('connections');

        $node
            ->fixXmlConfig('connection')
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

    protected function setupExchanges(ArrayNodeDefinition $node)
    {
        $node
            ->fixXmlConfig('exchange')
            ->append($this->getExchangesNode())
        ;
    }

    protected function getExchangesNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('exchanges');
        $node
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

    protected function setupQueues(ArrayNodeDefinition $node)
    {
        $node
            ->fixXmlConfig('queue')
            ->append($this->getQueuesNode())
        ;
    }

    protected function getQueuesNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('queues');
        $node
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
