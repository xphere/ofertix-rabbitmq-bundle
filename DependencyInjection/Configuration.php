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
        $treeBuilder = $this->getTreeBuilder();
        $rootNode = $treeBuilder->root($this->alias);
        $rootNode
            ->canBeDisabled()
            ->addDefaultsIfNotSet()
            ->append($this->setupConnections($rootNode))
            ->append($this->setupExchanges())
            ->append($this->setupQueues())
            ->append($this->setupProducers($rootNode))
            ->append($this->setupConsumers($rootNode))
        ;

        return $treeBuilder;
    }

    protected function setupConnections(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('default_connection')->defaultNull()->end()
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

        return $this->getTreeNode('connections')
            ->fixXmlConfig('connection')
            ->useAttributeAsKey('name')
            ->addDefaultChildrenIfNoneSet('default')

            ->prototype('array')
                ->children()
                    ->scalarNode('host')->defaultValue('localhost')->end()
                    ->integerNode('port')->defaultValue(5672)->end()
                    ->scalarNode('user')->defaultValue('guest')->end()
                    ->scalarNode('password')->defaultValue('guest')->end()
                    ->scalarNode('vhost')->defaultValue('/')->end()
                    ->booleanNode('lazy')->defaultTrue()->end()
                ->end()
            ->end()

            ->beforeNormalization()
                ->always(function($value) {
                    if (empty($value)) {
                        $value = array('default' => array());
                    }

                    return $value;
                })
            ->end()
        ;
    }

    protected function setupExchanges()
    {
        return $this->getTreeNode('exchanges')
            ->fixXmlConfig('exchange')
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->enumNode('type')
                        ->defaultValue('direct')
                        ->values(array('direct', 'fanout', 'topic', 'headers', ))
                    ->end()
                    ->booleanNode('passive')->defaultValue(false)->end()
                    ->booleanNode('durable')->defaultValue(false)->end()
                    ->booleanNode('auto_delete')->defaultValue(true)->end()
                    ->booleanNode('internal')->defaultValue(false)->end()
                    ->booleanNode('nowait')->defaultValue(false)->end()
                    ->variableNode('arguments')->defaultNull()->end()
                    ->scalarNode('ticket')->defaultNull()->end()
                ->end()
            ->end()
        ;
    }

    protected function setupQueues()
    {
        return $this->getTreeNode('queues')
            ->fixXmlConfig('queue')
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->booleanNode('passive')->defaultValue(false)->end()
                    ->booleanNode('durable')->defaultValue(false)->end()
                    ->booleanNode('exclusive')->defaultValue(false)->end()
                    ->booleanNode('auto_delete')->defaultValue(true)->end()
                    ->booleanNode('nowait')->defaultValue(false)->end()
                    ->variableNode('arguments')->defaultNull()->end()
                    ->scalarNode('ticket')->defaultNull()->end()
                ->end()
            ->end()
        ;
    }

    protected function setupChannel()
    {
        return $this->getTreeNode('channel', 'integer')
            ->beforeNormalization()
                ->ifTrue(function($v) {
                    return is_string($v) && is_numeric($v);
                })
                ->then(function($v) {
                    return intval($v, 10);
                })
            ->end()
            ->defaultValue('')
        ;
    }

    protected function setupProducers(ArrayNodeDefinition $rootNode)
    {
        $this->setDefaultConnection($rootNode, 'producers');

        return $this->getTreeNode('producers')
            ->fixXmlConfig('producer')
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('connection')->defaultNull()->end()
                    ->scalarNode('exchange')->isRequired()->end()
                    ->scalarNode('routing_key')->defaultValue('')->end()
                    ->booleanNode('mandatory')->defaultFalse()->end()
                    ->booleanNode('immediate')->defaultFalse()->end()
                    ->scalarNode('ticket')->defaultNull()->end()
                    ->append($this->setupChannel())
                    ->append($this->setupMessageParameters())
                    ->arrayNode('headers')->prototype('scalar')->end()
                ->end()
            ->end()
        ->end();
    }

    protected function setupMessageParameters()
    {
        return $this->getTreeNode('parameters')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('content_type')->defaultValue('text/plain')->end()
                ->scalarNode('content_encoding')->defaultValue('UTF-8')->end()
                ->enumNode('delivery_mode')
                    ->defaultValue('persistent')
                    ->values(array(1 => 'non-persistent', 2 => 'persistent'))
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
                ->integerNode('expiration')->defaultNull()->end()
                ->scalarNode('type')->defaultNull()->end()
                ->scalarNode('user_id')->defaultNull()->end()
                ->scalarNode('app_id')->defaultNull()->end()
            ->end()
        ;
    }

    protected function setupConsumers(ArrayNodeDefinition $rootNode)
    {
        $this->setDefaultConnection($rootNode, 'consumers');

        return $this->getTreeNode('consumers')
            ->fixXmlConfig('consumer')
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('connection')->defaultNull()->end()
                    ->scalarNode('exchange')->isRequired()->end()
                    ->scalarNode('queue')->isRequired()->end()
                    ->append($this->setupChannel())
                ->end()
            ->end()
        ;
    }

    protected function setDefaultConnection(ArrayNodeDefinition $node, $key)
    {
        $node
            ->validate()
                ->always(function($value) use ($key) {
                    foreach ($value[$key] as &$service) {
                        if (null === $service['connection']) {
                            $service['connection'] = $value['default_connection'];
                        }
                        unset($service);
                    }

                    return $value;
                })
            ->end()
        ->end();

        return $this;
    }

    protected function getTreeNode($name, $type = 'array')
    {
        return $this->getTreeBuilder()->root($name, $type);
    }

    protected function getTreeBuilder()
    {
        return new TreeBuilder();
    }
}
