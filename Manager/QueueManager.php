<?php

namespace Ofertix\RabbitMqBundle\Manager;

use PhpAmqpLib\Channel\AMQPChannel;

class QueueManager
{
    protected $queues = array();

    static protected $defaults = array(
        'name' => '',
        'passive' => false,
        'durable' => false,
        'exclusive' => false,
        'auto_delete' => true,
        'nowait' => false,
        'arguments' => null,
        'ticket' => null,
    );

    public function setQueue($name, $passive = false, $durable = false, $exclusive = false, $auto_delete = true, $nowait = false, $arguments = null, $ticket = null)
    {
        if (is_array($passive)) {
            $data = array_merge(self::$defaults, $passive);
            $data['name'] = $name;
        } else {
            $data = array(
                'name' => $name,
                'passive' => $passive,
                'durable' => $durable,
                'exclusive' => $exclusive,
                'auto_delete' => $auto_delete,
                'nowait' => $nowait,
                'arguments' => $arguments,
                'ticket' => $ticket,
            );
        }

        $this->queues[$name] = $data;
    }

    public function getQueue($name, AMQPChannel $channel)
    {
        if (false === array_key_exists($name, $this->queues)) {
            throw new \OutOfBoundsException(sprintf('Queue named "%s" not found', $name));
        }

        return call_user_func_array(array($channel, 'queue_declare'), $this->queues[$name]);
    }
}
