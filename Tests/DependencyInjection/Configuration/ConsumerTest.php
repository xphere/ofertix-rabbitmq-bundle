<?php

namespace Ofertix\RabbitMqBundle\Tests\DependencyInjection\Configuration;

class ConsumerTest extends ConfigurationAbstractTest
{
    public function testNoConsumersDefinedByDefault()
    {
        $result = $this->processConfig(array());

        $this->assertEmpty($result['consumers']);
    }

    public function testExchangesAndQueuesMustBeDefined()
    {
        $exchangeName = 'default_exchange';
        $queueName = 'default_queue';
        $config = array(
            'consumers' => array(
                'default_consumer' => array(
                    'exchange' => $exchangeName,
                    'queue' => $queueName,
                ),
            ),
        );
        $result = $this->processConfig($config);

        $consumer = $result['consumers']['default_consumer'];
        $this->assertEquals($exchangeName, $consumer['exchange']);
        $this->assertEquals($queueName, $consumer['queue']);
    }

    public function testUsesDefaultConnectionWhenNotSpecified()
    {
        $config = array(
            'connections' => array(
                'we_is_connections' => array(),
            ),
            'consumers' => array(
                'default_consumer' => array(
                    'exchange' => 'default_exchange',
                    'queue' => 'default_queue',
                ),
            ),
        );
        $result = $this->processConfig($config);

        $this->assertEquals('we_is_connections', $result['consumers']['default_consumer']['connection']);
    }

}
