<?php

namespace Ofertix\RabbitMqBundle\DependencyInjection;

use Ofertix\RabbitMqBundle\Consumer\Consumer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
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

        $this
            ->setupConnections($config, $container)
            ->setupProducers($config, $container)
            ->setupConsumers($config, $container)
        ;
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

        return $this;
    }

    protected function setupProducers(array $config, ContainerBuilder $container)
    {
        foreach ($config['producers'] as $name => $args) {
            $channel = $this->getChannel($args, $config, $container);
            $producer = new DefinitionDecorator('ofertix_rabbitmq.abstract_producer');
            $producer->setArguments(array($channel, $args['exchange'], $args['routing_key'], $args['mandatory'], $args['immediate'], $args['ticket'], $args['parameters'], $args['headers'], ));
            $container->setDefinition("ofertix_rabbitmq.producer.{$name}", $producer);
        }

        return $this;
    }

    protected function setupConsumers(array $config, ContainerBuilder $container)
    {
        foreach ($config['consumers'] as $name => $args) {
            $channel = $this->getChannel($args, $config, $container);
            $producer = new DefinitionDecorator('ofertix_rabbitmq.abstract_consumer');
            $producer->setArguments(array($channel, $args['queue'], $args['consumer_tag'], null));
            if (isset($args['qos'])) {
                $producer->addMethodCall('setQos', array($args['qos']['prefetch_size'], $args['qos']['prefetch_count']));
            }
            if (isset($args['flags'])) {
                $flags =
                    (isset($args['flags']['no_local'])  && $args['flags']['no_local']  ? Consumer::FLAG_NO_LOCAL  : 0) +
                    (isset($args['flags']['no_ack'])    && $args['flags']['no_ack']    ? Consumer::FLAG_NO_ACK    : 0) +
                    (isset($args['flags']['exclusive']) && $args['flags']['exclusive'] ? Consumer::FLAG_EXCLUSIVE : 0) +
                    (isset($args['flags']['nowait'])    && $args['flags']['nowait']    ? Consumer::FLAG_NOWAIT    : 0)
                ;
                $producer->addMethodCall('setFlags', array($flags));
            }
            $container->setDefinition("ofertix_rabbitmq.consumer.{$name}", $producer);
        }

        return $this;
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

        if (isset($data['exchange'])) {
            $exchange = $data['exchange'];
            $arguments = array_values($config['exchanges'][$exchange]);
            array_unshift($arguments, $exchange);
            $service->addMethodCall('exchange_declare', $arguments);
        }

        if (isset($data['queue'])) {
            $queue = $data['queue'];
            $arguments = array_values($config['queues'][$queue]);
            array_unshift($arguments, $queue);
            $service->addMethodCall('queue_declare', $arguments);
        }

        $container->setDefinition($serviceName, $service);

        return new Reference($serviceName);
    }
}
