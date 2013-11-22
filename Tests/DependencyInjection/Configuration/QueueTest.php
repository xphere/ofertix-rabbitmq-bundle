<?php

namespace Ofertix\RabbitMqBundle\Tests\DependencyInjection\Configuration;

class QueueTest extends ConfigurationAbstractTest
{
    /** @dataProvider providerValidQueues */
    public function testValidQueues(array $config, array $expected = null)
    {
        $result = $this->processConfig($config);

        $this->assertArrayHasKey('queues', $result);
        if (null !== $expected) {
            $this->assertEquals($expected, $result['queues']);
        }
    }

    public function providerValidQueues()
    {
        return array(
            'empty by default' => array(
                array(), array(),
            ),
            'empty when null' => array(
                array('queues' => null, ), array(),
            ),
            'one queue with default values' => array(
                array('queues' => array(
                    'default_queue' => null,
                )),
                array(
                    'default_queue' => array(
                        'passive' => false,
                        'durable' => false,
                        'exclusive' => false,
                        'auto_delete' => true,
                        'nowait' => false,
                        'arguments' => null,
                        'ticket' => null,
                    )
                ),
            ),
        );
    }
}
