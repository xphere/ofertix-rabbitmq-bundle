<?php

namespace Ofertix\RabbitMqBundle\Manager;

use PhpAmqpLib\Channel\AMQPChannel;

class ExchangeManager
{
    protected $exchanges = array();

    protected $defaults = array(
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

    public function getExchange($name, AMQPChannel $channel)
    {
        return call_user_func_array(array($channel, 'exchange_declare'), $this->optionsFor($name));
    }

    public function setExchange($name, $type = array(), $passive = false, $durable = false, $auto_delete = true, $internal = false, $nowait = false, $arguments = null, $ticket = null)
    {
        if (is_array($type)) {
            $knownOptions = array_key_exists($name, $this->exchanges) ? $this->exchanges[$name] : array();
            $options = array_merge($this->defaults, $knownOptions, $type);
            $options['name'] = $name;
        } else {
            $options = array(
                'name' => $name, 'type' => $type,
                'passive' => $passive,
                'durable' => $durable,
                'auto_delete' => $auto_delete,
                'internal' => $internal,
                'nowait' => $nowait,
                'arguments' => $arguments,
                'ticket' => $ticket,
            );
        }

        $this->exchanges[$name] = $options;
    }

    public function getExchangeNames()
    {
        return array_keys($this->exchanges);
    }

    public function optionsFor($name)
    {
        if (false === array_key_exists($name, $this->exchanges)) {
            throw new \OutOfBoundsException(sprintf('Exchange named "%s" not found', $name));
        }

        return $this->exchanges[$name];
    }
}
