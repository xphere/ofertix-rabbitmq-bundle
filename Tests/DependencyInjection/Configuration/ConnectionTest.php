<?php

namespace Ofertix\RabbitMqBundle\Tests\DependencyInjection\Configuration;

class ConfigurationTest extends ConfigurationAbstractTest
{
    static protected $connection_data = array(
        'default' => array(
            'host' => 'localhost',
            'port' => 5672,
            'user' => 'guest',
            'password' => 'guest',
            'vhost' => '/',
        ),
        'org' => array(
            'host' => 'example.org',
            'port' => 6725,
            'user' => 'anonymous',
            'password' => 'anonymous',
            'vhost' => '/rabbit-mq',
        ),
        'net' => array(
            'host' => 'example.net',
            'port' => 7256,
            'user' => 'myuser',
            'password' => 'mypassword',
            'vhost' => '/dev/shm/amqp',
        )
    );

    public function testGetDefaultConnection()
    {
        $expected = array(
            'default' => self::$connection_data['default'],
        );
        $result = $this->processConfig(array());

        $this->assertArrayHasKey('connections', $result);
        $this->assertEquals($expected, $result['connections']);
    }

    public function testConnections()
    {
        $expected = array(
            'connection_name' => self::$connection_data['org'],
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
            'default' => self::$connection_data['default'],
            'alternative' => self::$connection_data['net'],
        );
        $result = $this->processConfig(array(
            'connections' => $expected,
        ));

        $this->assertArrayHasKey('connections', $result);
        $this->assertEquals($expected, $result['connections']);
    }
}
