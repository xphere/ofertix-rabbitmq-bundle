<?php

namespace Ofertix\Tests;

use Ofertix\RabbitMqBundle\Producer\Producer;
use PhpAmqpLib\Channel\AMQPChannel;

class ProducerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerPublish
     */
    public function testPublish(array $config = array(), $message = '', array $msgprops = array())
    {
        /**
         * @var $exchange
         * @var $routing_key
         * @var bool $mandatory
         * @var bool $immediate
         * @var $ticket
         * @var array $properties
         * @var array $headers */
        $defaults = array(
            'exchange' => '',
            'routing_key' => '',
            'mandatory' => false,
            'immediate' => false,
            'ticket' => null,
            'properties' => array(),
            'headers' => array(),
        );
        extract(array_merge($defaults, $config), EXTR_OVERWRITE);

        $channel = $this
            ->getMockBuilder('PhpAmqpLib\Channel\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $channel
            ->expects($this->once())
            ->method('basic_publish')
            ->with($this->anything(), $exchange, $routing_key, $mandatory, $immediate, $ticket)
        ;
        /** @var AMQPChannel $channel */
        $producer = new Producer($channel, $exchange, $routing_key, $mandatory, $immediate, $ticket, $properties, $headers);
        $this->assertSame($producer, $producer->publish($message, $msgprops));
    }

    public function providerPublish()
    {
        return array(
            'defaults' => array(),
        );
    }
}
