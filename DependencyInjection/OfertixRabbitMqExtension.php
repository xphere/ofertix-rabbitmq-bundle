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

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (isset($config['default_connection']) && !isset($config['connections'][$config['default_connection']])) {
            throw new \UnexpectedValueException(
                'The default connection should be named "%s" but it does not exist'
            );
        }
        $connection = new DefinitionDecorator('ofertix_rabbitmq.abstract_connection');
        $container->setDefinition('ofertix_rabbitmq', $connection);
    }

    public function getAlias()
    {
        return 'ofertix_rabbitmq';
    }
}
