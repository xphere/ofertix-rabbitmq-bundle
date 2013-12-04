<?php

namespace Ofertix\RabbitMqBundle\Tests;

use Ofertix\RabbitMqBundle\Consumer\Consumer;

class ConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerConsume
     */
    public function testConsume(array $config = array())
    {
        /**
         * @var $queue
         * @var $consumer_tag
         * @var bool $flags
         * @var $callback
         * @var $prefetch_size
         * @var $prefetch_count
         */
        $default = array(
            'queue' => '',
            'consumer_tag' => '',
            'callback' => null,
            'flags' => 0,
            'prefetch_size' => null,
            'prefetch_count' => 1,
        );
        extract(array_merge($default, $config), EXTR_OVERWRITE);
        $no_local = !!($flags & Consumer::FLAG_NO_LOCAL);
        $no_ack = !!($flags & Consumer::FLAG_NO_ACK);
        $exclusive = !!($flags & Consumer::FLAG_EXCLUSIVE);
        $nowait = !!($flags & Consumer::FLAG_NOWAIT);

        $channel = $this
            ->getMockBuilder('PhpAmqpLib\Channel\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $channel
            ->expects($this->once())
            ->method('basic_qos')
            ->with($prefetch_size, $prefetch_count, null)
        ;
        $channel
            ->expects($this->once())
            ->method('basic_consume')
            ->with($queue, $consumer_tag, $no_local, $no_ack, $exclusive, $nowait, $callback)
        ;

        $consumer = new Consumer($channel, $queue, $consumer_tag, $callback);
        $consumer->setQos($prefetch_size, $prefetch_count);
        $consumer->setFlags($flags);
        $this->assertSame($consumer, $consumer->consume());
    }

    public function providerConsume()
    {
        return array(
            'defaults' => array(),
            'full example' => array(
                array(
                    'queue' => 'queue_name',
                    'consumer_tag' => 'consumer_tag',
                    'callback' => function() { },
                    'flags' => Consumer::FLAG_NO_LOCAL|Consumer::FLAG_NO_ACK|Consumer::FLAG_EXCLUSIVE|Consumer::FLAG_NOWAIT,
                    'prefetch_size' => 10,
                    'prefetch_count' => 100,
                ),
            ),
        );
    }
}
