<?php

namespace Ofertix\RabbitMqBundle\Producer;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Producer
{
    /** @var AMQPChannel */
    protected $channel;
    /** @var string */
    protected $exchange;
    /** @var string */
    protected $routing_key;
    /** @var bool */
    protected $mandatory;
    /** @var bool */
    protected $immediate;
    /** @var string */
    protected $ticket;
    /** @var array */
    protected $properties;
    /** @var array */
    protected $headers;

    public function __construct(AMQPChannel $channel, $exchange, $routing_key, $mandatory, $immediate, $ticket, array $properties, array $headers)
    {
        $this->channel = $channel;
        $this->exchange = $exchange;
        $this->routing_key = $routing_key;
        $this->mandatory = $mandatory;
        $this->immediate = $immediate;
        $this->ticket = $ticket;
        $this->properties = $properties;
        $this->headers = $headers;
    }

    public function publish($message, array $properties = array())
    {
        $properties = array_merge($properties, $this->properties);
        $message = new AMQPMessage($message, $properties);
        $this->channel->basic_publish($message, $this->exchange, $this->routing_key, $this->mandatory, $this->immediate, $this->ticket);

        return $this;
    }
}
