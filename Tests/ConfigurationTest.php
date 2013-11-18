<?php

namespace Ofertix\RabbitMqBundle\Tests;

use Ofertix\RabbitMqBundle as Bundle;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDefaultValues()
    {
        $result = $this->processConfig(array());

        $this->assertTrue(isset($result['connections']));
        $expected = array(
            'default' => array(
                'host' => 'localhost',
                'port' => 5672,
                'user' => 'guest',
                'password' => 'guest',
                'vhost' => '/',
            ),
        );
        $this->assertEquals($expected, $result['connections']);
    }

    protected function processConfig(array $config)
    {
        $configuration = $this->getConfiguration();
        $processor = $this->getProcessor();

        return $processor->processConfiguration($configuration, func_get_args());
    }

    protected function getConfiguration()
    {
        return new Bundle\DependencyInjection\Configuration();
    }

    protected function getProcessor()
    {
        return new Processor();
    }
}
