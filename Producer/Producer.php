<?php

namespace Ofertix\RabbitMqBundle\Producer;

use PhpAmqpLib\Channel\AMQPChannel;

class Producer
{
    /** @var AMQPChannel */
    protected $channel;
    /** @var array */
    protected $properties;

    public function __construct(AMQPChannel $channel, array $properties, array $headers)
    {
        $this->channel = $channel;
        $this->properties = $properties;
        $this->headers = $headers;
    }
}
