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
        );
    }
}
