<?php

namespace Ofertix\RabbitMqBundle\Manager;

use PhpAmqpLib\Channel\AMQPChannel;

class QueueManager
{
    protected $queues = array();

    public function setQueue($name, $passive = false, $durable = false, $exclusive = false, $auto_delete = true, $nowait = false, $arguments = null, $ticket = null)
    {
        $this->queues[$name] = func_get_args();
    }

    public function getQueue($name, AMQPChannel $channel)
    {
        return call_user_func_array(array($channel, 'queue_declare'), $this->queues[$name]);
    }
}
