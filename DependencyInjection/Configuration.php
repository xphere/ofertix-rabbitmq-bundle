<?php

namespace Ofertix\RabbitMqBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\IntegerNodeDefinition;
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
            ->append($this->setupProducers())
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
                    ->booleanNode('lazy')
                        ->defaultTrue()
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
                    ->enumNode('type')
                        ->defaultValue('direct')
                        ->values(array('direct', 'fanout', 'topic', 'headers', ))
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

    protected function setupChannel()
    {
        $node = new IntegerNodeDefinition('channel');

        $node
            ->beforeNormalization()
                ->ifTrue(function($v) {
                    return is_string($v) && is_numeric($v);
                })
                ->then(function($v) {
                    return intval($v, 10);
                })
            ->end()
            ->defaultValue('')
        ->end();

        return $node;
    }

    protected function setupProducers()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('producers');
        $node
            ->fixXmlConfig('producer')
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('connection')
                        ->defaultNull()
                    ->end()
                    ->append($this->setupChannel())
                    ->scalarNode('exchange')
                        ->defaultNull()
                    ->end()
                    ->arrayNode('parameters')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('content_type')
                                ->defaultValue('text/plain')
                            ->end()
                            ->scalarNode('content_encoding')
                                ->defaultValue('UTF-8')
                            ->end()
                            ->enumNode('delivery_mode')
                                ->defaultValue('persistent')
                                ->values(array(1 => 'non-persistent', 2 => 'persistent', ))
                                ->validate()
                                    ->always(function($value) {
                                        return $value === 'persistent' ? 2 : 1;
                                    })
                                ->end()
                            ->end()
                            ->integerNode('priority')
                                ->defaultValue(0)
                                ->validate()
                                    ->ifNotInArray(range(0, 9))
                                    ->thenInvalid('Message priority must be in range [0..9]')
                                ->end()
                            ->end()
                            ->integerNode('expiration')
                                ->defaultNull()
                            ->end()
                            ->scalarNode('type')
                                ->defaultNull()
                            ->end()
                            ->scalarNode('user_id')
                                ->defaultNull()
                            ->end()
                            ->scalarNode('app_id')
                                ->defaultNull()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('headers')
                        ->prototype('scalar')
                        ->end()
                    ->end()
        ;

        return $node;
    }
}
