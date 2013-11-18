<?php

namespace Ofertix\RabbitMqBundle\Tests;

use Ofertix\RabbitMqBundle as Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $container = $this->processConfig(array());
        $container->compile();

        $name = 'ofertix_rabbitmq';
        $this->assertTrue($container->hasDefinition($name));
        $this->assertEquals('PhpAmqpLib\Connection\AMQPConnection', $container->getDefinition($name)->getClass());
    }

    protected function processConfig(array $config)
    {
        $extension = $this->getExtension();
        $container = $this->getContainer();
        $extension->load($config, $container);

        return $container;
    }

    protected function getExtension()
    {
        return new Bundle\DependencyInjection\OfertixRabbitMqExtension();
    }

    protected function getContainer()
    {
        return new ContainerBuilder();
    }
}
