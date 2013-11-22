<?php

namespace Ofertix\RabbitMqBundle\Manager;

use PhpAmqpLib\Channel\AMQPChannel;

class QueueManager
{
    protected $queues = array();

    public function getQueue($name, AMQPChannel $channel)
    {
        return call_user_func_array(array($channel, 'queue_declare'), $this->getSettingsFor($name));
    }

    public function setQueue($name, $passive = false, $durable = false, $exclusive = false, $auto_delete = true, $nowait = false, $arguments = null, $ticket = null)
    {
        if (is_array($passive)) {
            $knownOptions = array_key_exists($name, $this->queues) ? $this->queues[$name] : array();
            $data = array_merge($this->getDefaultSettings(), $knownOptions, $passive);
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

    protected function getDefaultSettings()
    {
        return array(
            'name' => '',
            'passive' => false,
            'durable' => false,
            'exclusive' => false,
            'auto_delete' => true,
            'nowait' => false,
            'arguments' => null,
            'ticket' => null,
        );
    }

    protected function getSettingsFor($name)
    {
        if (false === array_key_exists($name, $this->queues)) {
            throw new \OutOfBoundsException(sprintf('Queue named "%s" not found', $name));
        }

        return $this->queues[$name];
    }
}
