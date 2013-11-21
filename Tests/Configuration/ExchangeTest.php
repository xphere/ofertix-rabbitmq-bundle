<?php

namespace Ofertix\RabbitMqBundle\Tests\Configuration;

class ExchangeTest extends ConfigurationAbstractTest
{
    /** @dataProvider providerValidExchanges */
    public function testValidExchanges(array $config, array $expected)
    {
        $result = $this->processConfig($config);

        $this->assertArrayHasKey('exchanges', $result);
        $this->assertEquals($expected, $result['exchanges']);
    }

    public function providerValidExchanges()
    {
        return array(
            'empty by default' => array(
                array(), array(),
            ),
            'empty when null' => array(
                array('exchanges' => null, ), array(),
            ),
            'one exchange with default values' => array(
                array('exchanges' => array(
                    'default_exchange' => null,
                )),
                array(
                    'default_exchange' => array(
                        'type' => 'direct',
                        'passive' => false,
                        'durable' => false,
                        'auto_delete' => true,
                        'internal' => false,
                        'nowait' => false,
                        'arguments' => null,
                        'ticket' => null,
                    )
                ),
            ),
        );
    }
}
