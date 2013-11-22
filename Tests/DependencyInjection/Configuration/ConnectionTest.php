<?php

namespace Ofertix\RabbitMqBundle\Tests\DependencyInjection\Configuration;

class ConfigurationTest extends ConfigurationAbstractTest
{
    /**
     * @dataProvider providerValidConnections
     * @param array $expected
     * @param array $config
     */
    public function testValidConnections(array $expected, array $config = null)
    {
        if (null === $config) {
            $config = array( 'connections' => $expected, );
        }
        $result = $this->processConfig($config);

        $this->assertArrayHasKey('connections', $result);
        $this->assertEquals($expected, $result['connections']);
    }

    public function providerValidConnections()
    {
        $default = array(
            'host' => 'localhost',
            'port' => 5672,
            'user' => 'guest',
            'password' => 'guest',
            'vhost' => '/',
        );
        $org = array(
            'host' => 'example.org',
            'port' => 6725,
            'user' => 'anonymous',
            'password' => 'anonymous',
            'vhost' => '/rabbit-mq',
        );
        $net = array(
            'host' => 'example.net',
            'port' => 7256,
            'user' => 'myuser',
            'password' => 'mypassword',
            'vhost' => '/dev/shm/amqp',
        );

        return array(
            'default values when empty config' => array(
                array('default' => $default, ),
                array(),
            ),
            'fully configured connection' => array(
                array('default' => $org, ),
            ),
            'fully configured connection with name' => array(
                array('connection_name' => $org, ),
            ),
            'multiple connections' => array(
                array('default' => $default, 'alternative' => $net, ),
            ),
        );
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
}
