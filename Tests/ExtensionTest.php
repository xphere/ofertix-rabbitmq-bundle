<?php

namespace Ofertix\RabbitMqBundle\Tests;

use Ofertix\RabbitMqBundle as Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $extension = $this->getExtension();
        $container = $this->processConfig($extension, array());
        $container->compile();

        $definition = $container->findDefinition('ofertix_rabbitmq');
        $this->assertEquals('PhpAmqpLib\Connection\AMQPConnection', $definition->getClass());
        $this->assertContains('localhost', $definition->getArguments());
    }

    public function testManuallySetDefaultConnection()
    {
        $configuration = array(
            'connections' => array(
                'my_connection' => array(
                    'host' => 'example.org',
                    'port' => 2567,
                    'user' => 'anonymous',
                    'password' => 'anonymous',
                    'vhost' => '/rabbit-mq',
                ),
            ),
            'default_connection' => 'my_connection',
        );

        $extension = $this->getExtension();
        $container = $this->processConfig($extension, array($configuration, ));
        $container->compile();

        $definition = $container->findDefinition('ofertix_rabbitmq');
        $this->assertEquals('PhpAmqpLib\Connection\AMQPConnection', $definition->getClass());
        $this->assertContains('example.org', $definition->getArguments());
    }

    public function testDefaultConnectionNameMustBeDefined()
    {
        $configuration = array(
            'connections' => array(
                'my_connection' => array(
                    'host' => 'example.org',
                    'port' => 2567,
                    'user' => 'anonymous',
                    'password' => 'anonymous',
                    'vhost' => '/rabbit-mq',
                ),
            ),
            'default_connection' => 'default',
        );

        $this->setExpectedException('UnexpectedValueException');

        $extension = $this->getExtension();
        $container = $this->processConfig($extension, array($configuration, ));
        $container->compile();
    }

    protected function processConfig($extension, array $config)
    {
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
