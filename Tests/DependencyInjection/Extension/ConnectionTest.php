<?php

namespace Ofertix\RabbitMqBundle\Tests\DependencyInjection\Extension;

class ConnectionTest extends ExtensionAbstractTest
{
    public function testDefaults()
    {
        $container = $this->processConfig(array());
        $definition = $container->findDefinition('ofertix_rabbitmq.connection.default');

        $this->assertSame($container->findDefinition('ofertix_rabbitmq'), $definition);
        $this->assertEquals('PhpAmqpLib\Connection\AMQPLazyConnection', $definition->getClass());
        $this->assertContains('localhost', $definition->getArguments());
    }

    public function testEmptyConnectionList()
    {
        $container = $this->processConfig(array(
            'connections' => array(
            ),
        ));
        $definition = $container->findDefinition('ofertix_rabbitmq.connection.default');

        $this->assertSame($container->findDefinition('ofertix_rabbitmq'), $definition);
        $this->assertEquals('PhpAmqpLib\Connection\AMQPLazyConnection', $definition->getClass());
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
        $definition = $container->findDefinition('ofertix_rabbitmq.connection.my_connection');

        $this->assertSame($container->findDefinition('ofertix_rabbitmq'), $definition);
        $this->assertEquals('PhpAmqpLib\Connection\AMQPLazyConnection', $definition->getClass());
        $this->assertContains('example.org', $definition->getArguments());
    }

    public function testManuallySetDefaultConnection()
    {
        $configuration = array(
            'connections' => array(
                'default' => array(),
                'my_connection' => array(
                    'host' => 'example.org',
                ),
            ),
            'default_connection' => 'my_connection',
        );

        $container = $this->processConfig($configuration);
        $definition = $container->findDefinition('ofertix_rabbitmq');

        $this->assertEquals('PhpAmqpLib\Connection\AMQPLazyConnection', $definition->getClass());
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

    public function testChannelGeneration()
    {
        $container = $this->processConfig(array(
            'connections' => array(
                'default_connection' => array(
                    'host' => 'localhost',
                    'test' => true,
                ),
            ),
            'exchanges' => array('default_exchange' => true, ),
            'producers' => array(
                'default_producer' => array(
                    'connection' => 'default_connection',
                    'channel' => '2576',
                    'exchange' => 'default_exchange',
                    'parameters' => array(
                        'delivery_mode' => 'persistent',
                    ),
                    'headers' => array(
                        'x-parameters' => true,
                    ),
                ),
            ),
        ));
    }
}
