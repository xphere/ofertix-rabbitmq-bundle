<?php

namespace Ofertix\RabbitMqBundle\Consumer;

use PhpAmqpLib\Channel\AMQPChannel;

class Consumer
{
    const FLAG_NO_LOCAL = 1;
    const FLAG_NO_ACK = 2;
    const FLAG_EXCLUSIVE = 4;
    const FLAG_NOWAIT = 8;

    /** @var AMQPChannel */
    protected $channel;
    protected $queue;
    protected $consumer_tag;
    protected $no_local;
    protected $no_ack;
    protected $exclusive;
    protected $nowait;
    protected $callback;
    protected $ticket;
    protected $prefetch_size = null;
    protected $prefetch_count = 1;

    public function __construct(AMQPChannel $channel, $queue = '', $consumer_tag = '', $callback = null, $ticket = null)
    {
        $this->channel = $channel;
        $this->queue = $queue;
        $this->consumer_tag = $consumer_tag;
        $this->callback = $callback;
        $this->ticket = $ticket;
    }

    public function setQos($prefetch_size, $prefetch_count)
    {
        $this->prefetch_size = $prefetch_size;
        $this->prefetch_count = $prefetch_count;

        return $this;
    }

    public function setFlags($flags)
    {
        $this->no_local = self::FLAG_NO_LOCAL === ($flags & self::FLAG_NO_LOCAL);
        $this->no_ack = self::FLAG_NO_ACK === ($flags & self::FLAG_NO_ACK);
        $this->exclusive = self::FLAG_EXCLUSIVE === ($flags & self::FLAG_EXCLUSIVE);
        $this->nowait = self::FLAG_NOWAIT === ($flags & self::FLAG_NOWAIT);

        return $this;
    }

    public function consume()
    {
        $this->channel->basic_qos($this->prefetch_size, $this->prefetch_count, null);
        $this->channel->basic_consume($this->queue, $this->consumer_tag, $this->no_local, $this->no_ack, $this->exclusive, $this->nowait, $this->callback, $this->ticket);

        return $this;
    }
}
