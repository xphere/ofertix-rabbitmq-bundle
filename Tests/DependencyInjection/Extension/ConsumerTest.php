<?php

namespace Ofertix\RabbitMqBundle\Tests\DependencyInjection\Extension;

class ConsumerTest extends ExtensionAbstractTest
{
    public function testFullExample()
    {
        $configuration = array(
            'connections' => array('default_connection' => array(), ),
            'exchanges' => array('default_exchange' => true, ),
            'queues' => array('default_queue' => true, ),
            'consumers' => array(
                'default_consumer' => array(
                    'connection' => 'default_connection',
                    'channel' => '2576',
                    'exchange' => 'default_exchange',
                    'queue' => 'default_queue',
                ),
            ),
        );

        $channel = $this->getMockBuilder('PhpAmqpLib\Channel\AMQPChannel')->disableOriginalConstructor()->getMock();
        $channel->expects($this->once())->method('exchange_declare');
        $channel->expects($this->once())->method('queue_declare');

        $connection = $this->getMockBuilder('PhpAmqpLib\Connection\AMQPConnection')->disableOriginalConstructor()->getMock();
        $connection->expects($this->once())->method('channel')->with(2576)->will($this->returnValue($channel));
        $this->mockService('ofertix_rabbitmq.connection.default_connection', $connection);

        $container = $this->processConfig($configuration);
        $container->get('ofertix_rabbitmq.consumer.default_consumer');
    }
}
