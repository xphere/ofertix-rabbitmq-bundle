<?php

namespace Ofertix\RabbitMqBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
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
        $this->setupProducers($config, $container);
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
            if ($data['lazy'] === true) {
                $connection->setClass($container->getParameter('ofertix_rabbitmq.lazy_connection.class'));
            }
            $connection
                ->setArguments(array(
                    $data['host'], $data['port'], $data['user'], $data['password'], $data['vhost']
                ))
            ;
            $container->setDefinition("ofertix_rabbitmq.connection.{$name}", $connection);
        }
    }

    protected function setupProducers(array $config, ContainerBuilder $container)
    {
        foreach ($config['producers'] as $name => $arguments) {
            $channel = $this->getChannel($arguments, $config, $container);
            $producer = new DefinitionDecorator('ofertix_rabbitmq.abstract_producer');
            $producer
                ->setArguments(array($channel, $arguments['parameters'], $arguments['headers']))
            ;
            $container->setDefinition("ofertix_rabbitmq.producer.{$name}", $producer);
        }
    }

    protected function getChannel(array $data, array $config, ContainerBuilder $container)
    {
        $connection = null !== $data['connection'] ? "ofertix_rabbitmq.connection.{$data['connection']}" : 'ofertix_rabbitmq';
        $channelName = null !== $data['channel'] ? $data['channel'] : '';
        $serviceName = 'ofertix_rabbitmq.channel.' . md5("{$connection}/{$channelName}/" . serialize($data));

        $service = new DefinitionDecorator('ofertix_rabbitmq.abstract_channel');
        $service
            ->setFactoryService($connection)
            ->setArguments(array($channelName, ))
            ->setAbstract(false)
        ;

        if (null !== $data['exchange']) {
            $arguments = array_values($config['exchanges'][$data['exchange']]);
            array_unshift($arguments, $data['exchange']);
            $service->addMethodCall('exchange_declare', $arguments);
        }

        $container->setDefinition($serviceName, $service);

        return new Reference($serviceName);
    }
}
