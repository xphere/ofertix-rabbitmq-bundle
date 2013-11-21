<?php

namespace Ofertix\RabbitMqBundle\Tests\Configuration;

class ExchangeTest extends ConfigurationAbstractTest
{
    public function testEmptyExchangeAllowed()
    {
        $expected = array();
        $result = $this->processConfig(array());

        $this->assertArrayHasKey('exchanges', $result);
        $this->assertEquals($expected, $result['exchanges']);
    }
}
