<?php

namespace Ofertix\RabbitMqBundle\Tests;

use Ofertix\RabbitMqBundle as Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $container = $this->processConfig(array());
        $definition = $container->findDefinition('ofertix_rabbitmq');

        $this->assertEquals('PhpAmqpLib\Connection\AMQPConnection', $definition->getClass());
        $this->assertContains('localhost', $definition->getArguments());
    }

    public function testAutodiscoverDefaultConnection()
    {
        $configuration = array(
            'connections' => array(
                'my_connection' => array(
                    'host' => 'example.org',
                ),
            ),
        );

        $container = $this->processConfig($configuration);
        $definition = $container->findDefinition('ofertix_rabbitmq');

        $this->assertEquals('PhpAmqpLib\Connection\AMQPConnection', $definition->getClass());
        $this->assertContains('example.org', $definition->getArguments());
    }

    public function testManuallySetDefaultConnection()
    {
        $configuration = array(
            'connections' => array(
                'my_connection' => array(
                    'host' => 'example.org',
                ),
            ),
            'default_connection' => 'my_connection',
        );

        $container = $this->processConfig($configuration);
        $definition = $container->findDefinition('ofertix_rabbitmq');

        $this->assertEquals('PhpAmqpLib\Connection\AMQPConnection', $definition->getClass());
        $this->assertContains('example.org', $definition->getArguments());
    }

    public function testDefaultConnectionNameMustBeDefined()
    {
        $configuration = array(
            'connections' => array(
                'my_connection' => array(),
            ),
            'default_connection' => 'default',
        );

        $this->setExpectedException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');

        $this->processConfig($configuration);
    }

    public function testDisable()
    {
        $configuration = array(
            'enabled' => false,
        );

        $this->setExpectedException('Symfony\Component\DependencyInjection\Exception\InvalidArgumentException');

        $container = $this->processConfig($configuration);
        $container->findDefinition('ofertix_rabbitmq');
    }

    protected function processConfig(array $config)
    {
        $extension = $this->getExtension();
        $container = $this->getContainer();
        $extension->load(func_get_args(), $container);
        $container->compile();

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