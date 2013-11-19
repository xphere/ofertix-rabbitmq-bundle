<?php

namespace Ofertix\RabbitMqBundle\Tests;

use Ofertix\RabbitMqBundle as Bundle;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDefaultConnection()
    {
        $expected = array(
            'default' => array(
                'host' => 'localhost',
                'port' => 5672,
                'user' => 'guest',
                'password' => 'guest',
                'vhost' => '/',
            ),
        );
        $result = $this->processConfig(array());

        $this->assertArrayHasKey('connections', $result);
        $this->assertEquals($expected, $result['connections']);
    }

    public function testConnections()
    {
        $expected = array(
            'connection_name' => array(
                'host' => 'example.org',
                'port' => 6725,
                'user' => 'anonymous',
                'password' => 'anonymous',
                'vhost' => '/rabbit-mq',
            ),
        );
        $result = $this->processConfig(array(
            'connections' => $expected,
        ));

        $this->assertArrayHasKey('connections', $result);
        $this->assertEquals($expected, $result['connections']);
    }

    public function testInvalidPort()
    {
        $this->setExpectedException('\Symfony\Component\Config\Definition\Exception\InvalidTypeException');

        $this->processConfig(array(
            'connections' => array(
                'default' => array(
                    'port' => '5672',
                ),
            ),
        ));
    }

    public function testMultipleConnections()
    {
        $expected = array(
            'default_connection' => array(
                'host' => 'example.org',
                'port' => 6725,
                'user' => 'anonymous',
                'password' => 'anonymous',
                'vhost' => '/rabbit-mq',
            ),
            'alternative_connection' => array(
                'host' => 'example.net',
                'port' => 7256,
                'user' => 'myuser',
                'password' => 'mypassword',
                'vhost' => '/dev/shm/amqp',
            ),
        );
        $result = $this->processConfig(array(
            'connections' => $expected,
        ));

        $this->assertArrayHasKey('connections', $result);
        $this->assertEquals($expected, $result['connections']);
    }

    protected function processConfig(array $config)
    {
        $configuration = $this->getConfiguration();
        $processor = $this->getProcessor();

        return $processor->processConfiguration($configuration, func_get_args());
    }

    protected function getConfiguration()
    {
        return new Bundle\DependencyInjection\Configuration('ofertix_rabbitmq_configuration');
    }

    protected function getProcessor()
    {
        return new Processor();
    }
}
