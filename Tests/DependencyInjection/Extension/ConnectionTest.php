<?php

namespace Ofertix\RabbitMqBundle\Tests\DependencyInjection\Extension;

class ConnectionTest extends ExtensionAbstractTest
{
    public function testDefaults()
    {
        $container = $this->processConfig(array());
        $definition = $container->findDefinition('ofertix_rabbitmq');

        $this->assertEquals('PhpAmqpLib\Connection\AMQPConnection', $definition->getClass());
        $this->assertContains('localhost', $definition->getArguments());
    }

    public function testEmptyConnectionList()
    {
        $container = $this->processConfig(array(
            'connections' => array(
            ),
        ));
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
}
