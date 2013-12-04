<?php

namespace Ofertix\RabbitMqBundle\Tests\DependencyInjection\Extension;

use Ofertix\RabbitMqBundle\DependencyInjection\OfertixRabbitMqExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class ExtensionAbstractTest extends \PHPUnit_Framework_TestCase
{
    private $mockedServices = array();

    protected function processConfig(array $config)
    {
        $extension = $this->getExtension();
        $container = $this->getContainer();
        $extension->load(func_get_args(), $container);
        $container->compile();

        return $container;
    }

    protected function getExtension()
    {
        return new OfertixRabbitMqExtension();
    }

    protected function mockService($name, $service)
    {
        $this->mockedServices[$name] = $service;

        return $this;
    }

    protected function getContainer()
    {
        $container = new ContainerBuilder();
        foreach ($this->mockedServices as $name => $service) {
            $container->set($name, $service);
        }

        return $container;
    }
}
