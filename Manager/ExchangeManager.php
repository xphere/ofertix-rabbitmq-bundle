<?php

namespace Ofertix\RabbitMqBundle\Manager;

use PhpAmqpLib\Channel\AMQPChannel;

class ExchangeManager
{
    protected $exchangeOptions = array();

    public function getExchange($name, AMQPChannel $channel)
    {
        if (false === array_key_exists($name, $this->exchangeOptions)) {
            throw new \OutOfBoundsException(sprintf('Exchange named "%s" not found', $name));
        }

        return call_user_func_array(array($channel, 'exchange_declare'), $this->exchangeOptions[$name]);
    }

    public function setExchange($name, $type, $passive = false, $durable = false, $auto_delete = true, $internal = false, $nowait = false, $arguments = null, $ticket = null)
    {
        $this->exchangeOptions[$name] = array(
            'name' => $name,
            'type' => $type,
            'passive' => $passive,
            'durable' => $durable,
            'auto_delete' => $auto_delete,
            'internal' => $internal,
            'nowait' => $nowait,
            'arguments' => $arguments,
            'ticket' => $ticket,
        );
    }
}
