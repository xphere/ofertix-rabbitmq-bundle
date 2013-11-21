<?php

namespace Ofertix\RabbitMqBundle\Tests\Extension;

use Ofertix\RabbitMqBundle\DependencyInjection\OfertixRabbitMqExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class ExtensionAbstractTest extends \PHPUnit_Framework_TestCase
{
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

    protected function getContainer()
    {
        return new ContainerBuilder();
    }
}
