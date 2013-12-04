<?php

namespace Ofertix\RabbitMqBundle\Tests\DependencyInjection\Configuration;

class ConsumerTest extends ConfigurationAbstractTest
{
    /** @dataProvider providerValidConsumers */
    public function testValidConsumers(array $config = array(), array $expected = null)
    {
        $result = $this->processConfig($config);

        $this->assertArrayHasKey('consumers', $result);
        if (null !== $expected) {
            $this->assertEquals($expected, $result['consumers']);
        }
    }

    public function providerValidConsumers()
    {
        return array(
            'default' => array(),
        );
    }
}
