<?php

namespace Ofertix\RabbitMqBundle\Manager;

use PhpAmqpLib\Channel\AMQPChannel;

class QueueManager
{
    public function setQueue($name)
    {
    }

    public function getQueue($name, AMQPChannel $channel)
    {
        $channel->queue_declare($name, false, false, false, true, false, null, null);
    }
}
