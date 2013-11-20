<?php

namespace Ofertix\RabbitMqBundle\Manager;

use PhpAmqpLib\Channel\AMQPChannel;

class ExchangeManager
{
    protected $exchangeOptions = array();

    public function getExchange($name, AMQPChannel $channel)
    {
        return call_user_func_array(array($channel, 'exchange_declare'), $this->optionsFor($name));
    }

    public function setExchange($name, $type = array(), $passive = false, $durable = false, $auto_delete = true, $internal = false, $nowait = false, $arguments = null, $ticket = null)
    {
        if (is_array($type)) {
            $default = array(
                'name' => '',
                'type' => 'direct',
                'passive' => false,
                'durable' => false,
                'auto_delete' => true,
                'internal' => false,
                'nowait' => false,
                'arguments' => null,
                'ticket' => null,
            );
            $options = array_merge(
                $default,
                array_key_exists($name, $this->exchangeOptions) ? $this->exchangeOptions[$name] : array(),
                $type
            );
            $options['name'] = $name;
        } else {
            $options = array(
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

        $this->exchangeOptions[$name] = $options;
    }

    public function optionsFor($name)
    {
        if (false === array_key_exists($name, $this->exchangeOptions)) {
            throw new \OutOfBoundsException(sprintf('Exchange named "%s" not found', $name));
        }

        return $this->exchangeOptions[$name];
    }
}
