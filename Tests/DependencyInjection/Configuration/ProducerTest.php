<?php

namespace Ofertix\RabbitMqBundle\Tests\DependencyInjection\Configuration;

class ProducerTest extends ConfigurationAbstractTest
{
    /** @dataProvider providerValidProducers */
    public function testValidProducers(array $config, array $expected)
    {
        $result = $this->processConfig($config);

        $this->assertArrayHasKey('producers', $result);
        $this->assertEquals($expected, $result['producers']);
    }

    public function providerValidProducers()
    {
        return array(
            'empty set has no producers' => array(
                array(),
                array(),
            ),
            'default producer' => array(
                array(
                    'producers' => array(
                        'producer_name' => array(
                            'exchange' => 'exchange_name',
                        ),
                    )
                ),
                array(
                    'producer_name' => array(
                        'connection' => 'default',
                        'exchange' => 'exchange_name',
                        'routing_key' => '',
                        'mandatory' => false,
                        'immediate' => false,
                        'ticket' => null,
                        'channel' => '',
                        'parameters' => array(
                            'content_type' => 'text/plain',
                            'content_encoding' => 'UTF-8',
                            'delivery_mode' => 'persistent',
                            'priority' => 0,
                            'expiration' => null,
                            'type' => null,
                            'user_id' => null,
                            'app_id' => null,
                        ),
                        'headers' => array(),
                    ),
                ),
            ),
        );
    }
}
