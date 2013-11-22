<?php

namespace Ofertix\RabbitMqBundle\Tests\DependencyInjection\Configuration;

use Ofertix\RabbitMqBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

abstract class ConfigurationAbstractTest extends \PHPUnit_Framework_TestCase
{
    protected function processConfig(array $config)
    {
        $configuration = $this->getConfiguration();
        $processor = $this->getProcessor();

        return $processor->processConfiguration($configuration, func_get_args());
    }

    protected function getConfiguration()
    {
        return new Configuration('ofertix_rabbitmq_configuration');
    }

    protected function getProcessor()
    {
        return new Processor();
    }
}
