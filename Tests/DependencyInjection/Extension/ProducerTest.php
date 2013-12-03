<?php

namespace Ofertix\RabbitMqBundle\Tests\DependencyInjection\Extension;

class ProducerTest extends ExtensionAbstractTest
{
    public function testFullExample()
    {
        $configuration = array(
            'connections' => array('default_connection' => array(), ),
            'exchanges' => array('default_exchange' => true, ),
            'producers' => array(
                'default_producer' => array(
                    'connection' => 'default_connection',
                    'channel' => '2576',
                    'exchange' => 'default_exchange',
                    'parameters' => array(
                        'delivery_mode' => 'persistent',
                    ),
                    'headers' => array(
                        'x-parameters' => true,
                    ),
                ),
            ),
        );

        $channel = $this->getMockBuilder('PhpAmqpLib\Channel\AMQPChannel')->disableOriginalConstructor()->getMock();
        $channel->expects($this->once())->method('exchange_declare');
        $channel->expects($this->once())->method('basic_publish');

        $connection = $this->getMockBuilder('PhpAmqpLib\Connection\AMQPConnection')->disableOriginalConstructor()->getMock();
        $connection->expects($this->once())->method('channel')->with(2576)->will($this->returnValue($channel));
        $this->mockService('ofertix_rabbitmq.connection.default_connection', $connection);

        $container = $this->processConfig($configuration);
        $container->get('ofertix_rabbitmq.producer.default_producer')->publish('message');
    }
}
