<?php

namespace Ofertix\RabbitMqBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class OfertixRabbitMqExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($this->getAlias());
        $config = $this->processConfiguration($configuration, $configs);

        if (false === $config['enabled']) {
            return;
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $this->setupConnections($config, $container);
        $this->setupExchanges($config, $container);
        $this->setupQueues($config, $container);
    }

    public function getAlias()
    {
        return 'ofertix_rabbitmq';
    }

    protected function setupConnections(array $config, ContainerBuilder $container)
    {
        $container->setAlias('ofertix_rabbitmq', "ofertix_rabbitmq.connection.{$config['default_connection']}");
        foreach ($config['connections'] as $name => $data) {
            $connection = new DefinitionDecorator('ofertix_rabbitmq.abstract_connection');
            $connection->setArguments(array($data['host'], $data['port'], $data['user'], $data['password'], $data['vhost']));
            $container->setDefinition("ofertix_rabbitmq.connection.{$name}", $connection);
        }
    }

    protected function setupExchanges(array $config, ContainerBuilder $container)
    {
        $definition = $container->findDefinition('ofertix_rabbitmq.exchange_manager');
        foreach ($config['exchanges'] as $name => $arguments) {
            array_unshift($arguments, $name);
            $definition->addMethodCall('setExchange', $arguments);
        }
    }

    protected function setupQueues(array $config, ContainerBuilder $container)
    {
        $definition = $container->findDefinition('ofertix_rabbitmq.queue_manager');
        foreach ($config['queues'] as $name => $arguments) {
            array_unshift($arguments, $name);
            $definition->addMethodCall('setQueue', $arguments);
        }
    }
}
