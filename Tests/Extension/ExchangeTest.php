<?php

namespace Ofertix\RabbitMqBundle\Tests\Extension;

class ExchangeTest extends ExtensionAbstractTest
{
    public function testDefaults()
    {
        $container = $this->processConfig(array());
        $manager = $container->get('ofertix_rabbitmq.exchange_manager');

        $this->assertEquals('Ofertix\RabbitMqBundle\Manager\ExchangeManager', get_class($manager));
        $this->assertEmpty($manager->getExchangeNames());
    }

    public function testDefinedExchanges()
    {
        $container = $this->processConfig(array(
            'exchanges' => array(
                'first' => null,
                'second' => null,
            ),
        ));
        $manager = $container->get('ofertix_rabbitmq.exchange_manager');

        $this->assertEquals('Ofertix\RabbitMqBundle\Manager\ExchangeManager', get_class($manager));
        $this->assertEquals(array('first', 'second', ), $manager->getExchangeNames());
    }
}
